<?php

namespace HG\SettingsBundle\Model;

use HG\UtilsBundle\Utils\HGUtils;

class SettingsManager
{
  /**
   * statikus tömb a beállítások memóriába való betöltéséhez
   *
   * @var array
   */
  private $settings = array();

  /**
  * A user
  *
  * @var sfUser
  */
  protected $request;

  protected $cache;

  protected $uploadPath;

  protected $entityManager;
  protected $locales;
  protected $container;
  protected $types;

  public function __construct($cache, $container, $entityManager)
  {
    $this->cache = $cache;
    $this->container = $container;
    $this->request = $container->get('request');
    $this->entityManager = $entityManager;
    $this->uploadPath = $container->getParameter('hg_settings.upload_dir');
    $this->locales = $container->getParameter('hg_utils.available_locales');
    $this->types = $container->getParameter('hg_settings.types');
  }
  /**
   * Az osztály statikus $setting tömbjébe rakja a beállítást a név útvonalon
   * ha már van ilyen, akkor felülírja, nem törődik a régi értékkel
   *
   * @param string $settingName
   * @param string $settingValue
   * @return bool
   */
  protected function addToRuntime($settingName, $settingValue)
  {
    $this->settings[$settingName] = $settingValue;

    return true;
  }

  /**
   * Az osztály statikus tömbjéből visszaadja a kért beállítást
   *
   * @param string $settingName
   *
   * @return string
   */
  protected function getFromRuntime($settingName)
  {
    return $this->settings[$settingName][$this->request->getLocale()];
  }

  /**
   * megnézi, hogy van-e adott beállítás a tömbben
   *
   * @param string $settingName
   * @return bool
   */
  protected function hasOneRuntime($settingName)
  {
    if (array_key_exists($settingName, $this->settings))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * megnézi, hogy az arrayben kapott nevű beállítások be vannak-e már töltve a tömbbe   *
   *
   * @param array $settingsNameArray
   * @return bool
   */
  protected function hasRuntime($settingsNameArray)
  {
    $hasFlag = true;

    foreach ($settingsNameArray as $settingName)
    {
      if (!$this->hasOneRuntime($settingName))
      {
        $hasFlag = false;
      }
    }

    return $hasFlag;
  }


  /**
   * betölti a cacheből a kívánt beállításokat a requestbe
   *
   * @param array $settingsNameArray
   * @return bool
   */
  protected function loadSettingsFromCacheToRuntime($settingsNameArray)
  {
    // ellenőrizzük, hogy a $settings array-e
    $this->validateArrayParameter($settingsNameArray);

    // végigmegyünk a beállításokon és belerakjuk egy objektumtömbbe név szerint
    foreach ($settingsNameArray as $settingName)
    {
      $this->addToRuntime($settingName, $this->cache->getSetting($settingName));
    }

    return true;
  }

  /**
   * betölt egy adatbázisból kapott settingcsomagot a fileCache-be
   *
   * @param array $resultSet
   * @return bool $flag
   */
  protected function loadSettingsToCache($resultArray)
  {
    $flag = true;

    foreach ($resultArray as $name => $resArray)
    {
      if (!$this->cache->setSetting($name, $resArray))
      {
        $flag = false;
      }
    }

    return $flag;
  }

  /**
   * betölt egy adatbázisból kapott settingcsoamgot a requestbe
   *
   * @param obj $resultSet
   * @return void
   */
  protected function loadSettingsToRuntime($resultArray)
  {
    $flag = true;

    foreach ($resultArray as $name => $resArray)
    {
      if (!$this->addToRuntime($name, $resArray))
      {
        $flag = false;
      }
    }

    return $flag;
  }

  /**
   * megnézi, hogy array-e a paraméter, ha nem és string,
   * akkor array-é alakítja és visszaadja, ha egyik sem, akkor hibát dob
   *
   * @param array $param
   * @return array $param
   */
  protected function validateArrayParameter($param)
  {
    if (is_string($param))
    {
      return array($param);
    }
    elseif (is_array($param))
    {
      return $param;
    }
    else
    {
      throw new \Exception("Nem megfelelő 'sfSettingHandler' paraméterezés!");
    }
  }

  /**
   * megnézi, hogy string-e a paraméter, ha nem akkor hibát dob
   *
   * @param string $param
   * @return string
   */
  protected function validateStringParameter($param)
  {
    if (is_string($param))
    {
      return $param;
    }
    elseif ($param == null)
    {
      return null;
    }
    elseif ($param == "")
    {
      throw new \Exception("Nem megfelelő 'sfSettingHandler' paraméterezés!");
    }
    else
    {
      throw new \Exception("Nem megfelelő 'sfSettingHandler' paraméterezés!");
    }
  }

  /**
   * static method to pre-load the specified settings into memory. Use this
   * early in execution to avoid multiple SQL calls for individual settings.
   * Takes either a string or an array of strings as an argument.
   *
   * @param mixed $settings
   * @return bool
   */
  public function load($settingsNameArray)
  {
    // paraméterek helyességének ellenőrzése
    // $settings ellenőrzése
      $settingsNameArray = $this->validateArrayParameter($settingsNameArray);


    // utánanézünk, hogy a beállítások benne vannak-e a memóriában (static sfSetting osztály)
    if ($this->hasRuntime($settingsNameArray))
    {
      // ha igen, akkor minden oké, visszatérünk
      return true;
    }
    // ha valamely beállítás hiányzott akkor
    // utánanézünk, hogy a fileCache-ben benne van-e az összes kért beállítás
    elseif ($this->cache->hasSettings($settingsNameArray))
    {
      // betöltjük az sfSettingHandler statikus osztályba a meglévő beállításokat
      $this->loadSettingsFromCacheToRuntime($settingsNameArray);
    }
    // ha valamely paraméter nincs a fileCache-ben, így betöltjük újra mindet
    else
    {
      return $this->getAllSettings();
    }
  }

  /**
   * visszaadja a kért beállítást
   * ha nem találja egy fallback soron megy keresztül:
   * - hgabkaSfSettingHandler setting tömb
   * - fileCache
   * - load() - végső soron adatbázisból kapja meg
   *
   * @param string $settingName
   * @return object sfSetting
   */
  public function getSetting($settingName)
  {
    // megnézzük, hogy jó-e a paraméterezés ($settingName)
    $settingName = $this->validateStringParameter($settingName);

    if ($this->hasOneRuntime($settingName))
    {
      // ha igen, akkor vissza is térünk vele
      return $this->getFromRuntime($settingName);
    }
    // ha ott nincs, akkor megnézzük a fileCache-ben
    elseif ($this->cache->hasSetting($settingName))
    {
      // ha a fileCache-ben van, akkor vissza is adjuk
      $values = $this->cache->getSetting($settingName);

      return $values[$this->request->getLocale()];
    }
    else
    {
      // ha nem voltak betöltve, akkor betöltjük a load függvénnyel
      if ($this->load($settingName))
      {
        // a beállítás be lett töltve, visszaadhatjuk
        if ($this->hasOneRuntime($settingName))
        {
          return $this->getFromRuntime($settingName);
        }
        else
        {
          return null;
        }
      }
    }
  }

  /**
   * lekéri a kívánt beállítást
   * ha nincs ilyen, akkor visszaadja a default értéket
   *
   * @param string $settingName
   * @param string $defaultValue
   * @return string
   */
  public function get($settingName, $defaultValue = null)
  {
    $setting = $this->getSetting($settingName);

    return is_null($setting) ? $defaultValue : $setting;
  }

  /**
   * Visszaadja a file elérési útját
   *
   * @param string $settingName
   * @param string $defaultValue
   * @return string
   */
  public function getFilePath($settingName, $defaultValue = null)
  {
    return $this->uploadPath.DIRECTORY_SEPARATOR.$this->get($settingName, $defaultValue);
  }

  /**
   * Az összes beállítást  adja vissza egy tömbben
   * Rögtön adatbázishoz fordul, nem ellenőriz
   * A lekérdezés után elcacheli az eredményt, és visszaadja az eredménytömböt
   *
   * @return array
   */
  public function getAllSettings()
  {
    // nem ellenőrizzük, hogy mik vannak már betöltve, egyszerűbb kapásból az adatbázishoz fordulni

    $resultArray = array();

    $result = $this->entityManager->getRepository('HGSettingsBundle:Setting')->findAll();

    // ha kaptunk vissza valamit, akkor eltároljuk a fileCache-ben és a statikus tömbben
    if (count($result) > 0)
    {
      $resultArray = $this->convertObjectsToArray($result);

      if ($this->loadSettingsToCache($resultArray))
      {
        $this->loadSettingsToRuntime($resultArray);
      }

      // miután benne van minden beállítás a statikus tömbben visszaadjuk a tömböt
      return $this->settings;
    }
    else
    {
      return null;
    }
  }

  /**
   * Az összes beállítást tartalmazó tömböt ad vissza
   * formátum: $beallitasok[beallitasNev]
   * Ha nincsen beállítás eltárolva, akkor üres tömbbel tér vissza
   *
   * @return void
   */
  public function getAll()
  {

    $result = $this->getAllSettings();

    return is_null($result) ? array() : $result;
  }


  /**
  * A lekért objektumból tömböt készít
  *
  * @param array $result - array (nev1 => array(culture1 => ertek, culture2 => ertek, ...), nev2 => ...)
  */
  protected function convertObjectsToArray($result)
  {
    $resultArray = array();

    foreach ($result as $setting)
    {
      foreach ($this->locales as $culture)
      {
        $resultArray[$setting->getName()][$culture] = $setting->getValueByCulture($culture);
      }
    }

    return $resultArray;
  }

  public function getLocales()
  {
    return $this->locales;
  }

  public function clearCache()
  {
    $this->cache->clear();
  }

  public function getTypes()
  {
    return $this->types;
  }

  public function getTypeChoices()
  {
    $choices = array();
    foreach ($this->types as $name => $data)
    {
      $choices[$name] = $data['label'];
    }

    return $choices;
  }

  public function getOptionsArrayForSetting($setting)
  {
    return array('' => '') + (is_array($setting->getOptions()) ? $setting->getOptions() : HGUtils::stringToArray($setting->getOptions()));
  }

  
  public function getFileManager()
  {
    return $this->container->get('hg_file_repository.filemanager');
  }


}
