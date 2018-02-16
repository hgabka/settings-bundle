<?php

namespace Hgabka\SettingsBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Doctrine\Common\Inflector\Inflector;

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
        if (!$cache->has(self::CACHE_KEY)) {
            return $this->regenerateCache();
        }

        return $cache->get(self::CACHE_KEY, []);
    }

    /**
     * Kulcs-érték pár hozzáadása a cache-hez.
     *
     * @param string $name
     * @param mixed $value
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
        if (array_key_exists($name, $data)) {
            unset($data[$name]);

            return $cache->set(self::CACHE_KEY, $data);
        }

        return true;
    }

    public function regenerateCache()
    {
        $cache = $this->getCache();
        $cache->clear();
        $data = [];
        foreach ($this->doctrine->getRepository(Setting::class)->findAll() as $setting) {
            $data[$setting->getName()] = $setting->getValue();
        }
        $cache->set(self::CACHE_KEY, $data);

        return $data;
    }

    public function get($name, $defaultValue = null)
    {
        if (empty($this->settings)) {
            $this->settings = $this->getCacheData();
        }

        return array_key_exists($name, $this->settings) ? $this->settings[$name] : $defaultValue;
    }

    protected function getCache()
    {
        if (null === $this->cache) {
            $this->cache = new FilesystemCache(self::CACHE_KEY, 0, $this->cacheDir . DIRECTORY_SEPARATOR . 'systemsetting');
        }

        return $this->cache;
    }

    public function getLocales()
    {
        return $this->utils->getAvailableLocales();
    }
}