<?php

namespace Hgabka\SettingsBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\Common\Persistence\ManagerRegistry;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Model\SettingTypeInterface;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Validator\Constraint;

/**
 * Class SettingsManager.
 */
class SettingsManager
{
    const CACHE_KEY = 'systemsettings';
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
     * @var FilesystemCache
     */
    protected $cache;

    /**
     * @var array
     */
    protected $types = [];

    /**
     * SettingsManager constructor.
     *
     * @param Registry $doctrine
     * @param $cacheDir
     */
    public function __construct(ManagerRegistry $doctrine, HgabkaUtils $utils, $cacheDir)
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
            throw new \Exception('Ismeretlen, vagy nem elerheto metodus: '.self::class.'::'.$method);
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
        if (!$cache->has(self::CACHE_KEY)) {
            return $this->regenerateCache();
        }

        return $cache->get(self::CACHE_KEY, []);
    }

    /**
     * Kulcs-érték pár hozzáadása a cache-hez.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function addToCache($name, $value)
    {
        $cache = $this->getCache();
        $data = $cache->get(self::CACHE_KEY, []);
        $data[$name] = $value;

        return $cache->set(self::CACHE_KEY, $data);
    }

    /**
     * Kulcs-érték pár hozzáadása a cache-hez.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function addSettingToCache(Setting $setting)
    {
        $cache = $this->getCache();
        $data = $cache->get(self::CACHE_KEY, []);
        $data[$setting->getName()] = $this->convertToCache($setting);

        return $cache->set(self::CACHE_KEY, $data);
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
        $data = $cache->get(self::CACHE_KEY, []);
        if (\array_key_exists($name, $data)) {
            unset($data[$name]);

            return $cache->set(self::CACHE_KEY, $data);
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
        $this->getCache()->set(self::CACHE_KEY, $data);

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
     * @param SettingTypeInterface $type
     * @param                      $alias
     */
    public function addType(SettingTypeInterface $type, $alias)
    {
        $this->types[$alias] = $type;
        usort($this->types, function ($type1, $type2) {
            $p1 = null === $type1->getPriority() ? PHP_INT_MAX : $type1->getPriority();
            $p2 = null === $type2->getPriority() ? PHP_INT_MAX : $type2->getPriority();

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
     * @param Setting $setting
     * @param         $values
     */
    public function setValuesByCultures(Setting $setting, $values)
    {
        $type = $this->getType($setting->getType());
        foreach ($this->getLocales() as $locale) {
            $setting->translate($locale)->setValue($values[$locale] ? $type->transformValue($values[$locale]) : null);
        }
    }

    /**
     * @param array $options
     * @param       $constraints
     * @param bool  $setUnique
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

    /**
     * @param array      $options
     * @param Constraint $constraint
     */
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
     * @return FilesystemCache
     */
    protected function getCache()
    {
        if (null === $this->cache) {
            $this->cache = new FilesystemCache(self::CACHE_KEY, 0, $this->cacheDir.\DIRECTORY_SEPARATOR.'systemsetting');
        }

        return $this->cache;
    }

    /**
     * @param Setting $setting
     *
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
}
