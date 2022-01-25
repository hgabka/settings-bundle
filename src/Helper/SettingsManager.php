<?php

namespace Hgabka\SettingsBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Inflector\Inflector;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Model\SettingTypeInterface;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Validator\Constraint;

/**
 * Class SettingsManager.
 */
class SettingsManager
{
    public const CACHE_KEY = 'systemsettings';
    /**
     * @var Registry
     */
    protected $doctrine;

    /** @var HgabkaUtils */
    protected $utils;
    /**
     * @var string
     */
    protected $cacheDir;
    /**
     * @var array
     */
    protected $settings;
    /**
     * @var FilesystemAdapter
     */
    protected $cache;

    /**
     * @var array
     */
    protected $types = [];

    protected $cachedValues = [];

    /**
     * SettingsManager constructor.
     *
     * @param Registry $doctrine
     * @param $cacheDir
     */
    public function __construct(Registry $doctrine, HgabkaUtils $utils, $cacheDir)
    {
        $this->doctrine = $doctrine;
        $this->cacheDir = $cacheDir;
        $this->utils = $utils;
    }

    /**
     * Magic method a getter cuccoknak.
     *
     * @param mixed $method
     * @param mixed $arguments
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ('get' !== ($verb = substr($method, 0, 3))) {
            throw new \Exception('Ismeretlen, vagy nem elerheto metodus: ' . self::class . '::' . $method);
        }
        $property = substr($method, 3);
        $setting = Inflector::tableize($property);

        return $this->get($setting);
    }

    /**
     * Az összes beállítás lekérdezése a cache-ből.
     *
     * @return array
     */
    public function getCacheData()
    {
        $cache = $this->getCache();
        if (!$cache->hasItem(self::CACHE_KEY)) {
            return $this->regenerateCache();
        }

        return $cache->getItem(self::CACHE_KEY)->get() ?? [];
    }

    /**
     * Beállítás hozzáadása a cache-hez.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function addSettingToCache(Setting $setting)
    {
        $cache = $this->getCache();
        if ($cache->hasItem(self::CACHE_KEY)) {
            $item = $cache->getItem(self::CACHE_KEY);

            $data = $item->get() ?? [];
            $data[$setting->getName()] = $this->convertToCache($setting);

            $item->set($data);

            return $cache->save($item);
        }

        return true;
    }

    /**
     * Beállítás törlése a cache-ből.
     *
     * @param string $name
     *
     * @return bool
     */
    public function removeFromCache($name)
    {
        $cache = $this->getCache();
        if ($cache->hasItem(self::CACHE_KEY)) {
            $item = $cache->getItem(self::CACHE_KEY);
            $data = $item->get() ?? [];
            if (\array_key_exists($name, $data)) {
                unset($data[$name]);
            }
            $item->set($data);

            return $cache->save($item);
        }

        return true;
    }

    public function clearCache()
    {
        $cache = $this->getCache();

        $cache->clear();
    }

    /**
     * @return array
     */
    public function regenerateCache()
    {
        $this->clearCache();
        $data = [];
        foreach ($this->doctrine->getRepository(Setting::class)->findAll() as $setting) {
            $data[$setting->getName()] = $this->convertToCache($setting);
        }

        $item = $this->getCache()->getItem(self::CACHE_KEY);
        $item->set($data);

        $this->getCache()->save($item);

        return $data;
    }

    /**
     * @param      $name
     * @param null $locale
     * @param null $defaultValue
     *
     * @return null|mixed
     */
    public function get($name, $locale = null, $defaultValue = null)
    {
        if (empty($this->settings)) {
            $this->settings = $this->getCacheData();
        }

        if (\array_key_exists($name, $this->settings)) {
            $locale = $this->utils->getCurrentLocale($locale);

            $data = $this->settings[$name];
            $type = $this->getType($data['type']);

            return $type->reverseTransformValue($data['value'][$locale]);
        }

        return $defaultValue;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->utils->getAvailableLocales();
    }

    /**
     * @param $alias
     */
    public function addType(SettingTypeInterface $type, $alias)
    {
        $this->types[$alias] = $type;
        usort($this->types, function ($type1, $type2) {
            $p1 = null === $type1->getPriority() ? \PHP_INT_MAX : $type1->getPriority();
            $p2 = null === $type2->getPriority() ? \PHP_INT_MAX : $type2->getPriority();

            return $p1 <=> $p2;
        });
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return array
     */
    public function getTypeChoices()
    {
        $res = [];
        foreach ($this->types as $type) {
            if ($type->isVisible()) {
                $res[$type->getName()] = $type->getId();
            }
        }

        return $res;
    }

    /**
     * @param $typeId
     *
     * @return null|SettingTypeInterface
     */
    public function getType($typeId)
    {
        if (empty($typeId)) {
            throw new \InvalidArgumentException('Empty setting type id');
        }

        foreach ($this->types as $type) {
            if ($type->getId() === $typeId) {
                return $type;
            }
        }

        throw new \InvalidArgumentException(sprintf('The setting type with id "%s" does not exist. To create the type create a class that implements %s with the getId method returning this id', $typeId, SettingTypeInterface::class));
    }

    /**
     * @param $values
     */
    public function setValuesByCultures(Setting $setting, $values)
    {
        $type = $this->getType($setting->getType());
        foreach ($this->getLocales() as $locale) {
            $setting->translate($locale)->setValue($values[$locale] ? $type->transformValue($values[$locale]) : null);
        }
    }

    /**
     * @param      $constraints
     * @param bool $setUnique
     */
    public function addConstraints(array &$options, $constraints, $setUnique = true)
    {
        if (empty($constraints)) {
            return;
        }

        if ($constraints instanceof Constraint) {
            $constraints = [$constraints];
        }

        if (!isset($options['constraints'])) {
            $options['constraints'] = [];
        }

        if (!$setUnique) {
            $options['constraints'] = array_merge($options['constraints'], $constraints);

            return;
        }

        foreach ($constraints as $constraint) {
            $this->removeConstraint($options, $constraint);
            $options['constraints'][] = $constraint;
        }
    }

    public function removeConstraint(array &$options, Constraint $constraint)
    {
        if (empty($options['costraints'])) {
            return;
        }
        $cc = \get_class($constraint);
        foreach ($options['constraints'] as $k => $c) {
            if (\get_class($c) === $cc) {
                unset($options['constraints'][$k]);
            }
        }
    }

    /**
     * @return FilesystemAdapter
     */
    protected function getCache()
    {
        if (null === $this->cache) {
            $this->cache = new FilesystemAdapter(self::CACHE_KEY, 0, $this->cacheDir);
        }

        return $this->cache;
    }

    /**
     * @return array
     */
    protected function convertToCache(Setting $setting)
    {
        $res = ['value' => [], 'type' => $setting->getType()];
        $type = $this->getType($setting->getType());
        foreach ($this->getLocales() as $locale) {
            if ($setting->isCultureAware()) {
                $res['value'][$locale] = $setting->getValue($locale);
            } else {
                $res['value'][$locale] = $setting->getGeneralValue();
            }
        }

        return $res;
    }

    public function getValues(?string $locale = null)
    {
        $locale = $this->utils->getCurrentLocale($locale);
        if (!array_key_exists($locale, $this->cachedValues)) {
            $this->cachedValues[$locale] = [];
            $cacheData = $this->getCacheData();

            if (!empty($cacheData)) {

                foreach ($cacheData as $name => $data) {
                    $type = $this->getType($data['type']);

                    $this->cachedValues[$locale][$name] = $type->reverseTransformValue($data['value'][$locale]);
                }
            }
        }

        return $this->cachedValues[$locale];
    }

    public function replaceSettings(string $target, string $prefix = '', string $postfix = '', ?callable $callable = null, ?string $locale = null): string
    {
        $pairs = [];
        foreach ($this->getValues($locale) as $name => $value) {
            if (is_callable($callable)) {
                $value = $callable($value, $name);
            }

            if (is_scalar($value) && !is_bool($value)) {
                $pairs[$prefix.$name.$postfix] = (string)$value;
            }
        }

        return strtr($target, $pairs);
    }
}
