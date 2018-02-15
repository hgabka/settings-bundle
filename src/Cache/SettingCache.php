<?php
  
namespace HG\SettingsBundle\Cache;

use Doctrine\Common\Cache\FilesystemCache;

class SettingCache
{
  /**
   * Cache példányt tartalmazó változó
   *
   * @var sfFileCache
   */
  private $cache = '';

  public function __construct($cacheDir)
  {
    $this->cache = new FileSystemCache($cacheDir, '.HGSettingsCache');
  } 
  /**
   * Visszaadja a cache objektumot
   *
   * @return sfFileCache
   */
  public function getCacheObject()
  {
    return $this->cache;  
  }

  /**
   * Visszaadja, hogy van-e beállítás az adott néven
   *
   * @param string $settingName
   * @return bool
   */
  public function hasSetting($settingName)
  {
    return $this->cache->contains($settingName);
  }

  /**
   * Visszaadja, hogy van-e beállítás minden paraméterként kapott néven
   *
   * @param array $settingsArray
   * @param string $settingEnv
   * @return bool
   */
  public function hasSettings($settingsArray)
  {
    $hasFlag = true;

    foreach ($settingsArray as $setting)
    {
      if (!$this->hasSetting($setting))
      {
        $hasFlag = false;
      }
    }

    return $hasFlag;
  }

  /**
   * Visszaadja a beállítást
   *
   * @param string $settingName
   * @param string $settingEnv
   * @return array
   */
  public function getSetting($settingName)
  {
    // elkérjük a cahceből a beállítást és visszaalakítjuk tömbbé
    return unserialize($this->cache->fetch($settingName));
  }

  /**
   * Beállítja a beállítást
   * 
   * @param string $settingName
   * @param string $settingEnv
   * @param array $value
   * @return bool
   */
  public function setSetting($settingName, $value)
  {
    // eltároljuk a cacheben a serializált tömböt
    return $this->cache->save($settingName, serialize($value));
  }

  /**
   * Üríti a cache objektumot
   * 
   * @return bool
   */
  public function clear()
  {
    return $this->cache->deleteAll();
  }
}
  
