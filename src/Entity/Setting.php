<?php

namespace HG\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use HG\FileRepositoryBundle\File\FileRepositoryUploadRequestInterface;
use HG\FileRepositoryBundle\Entity\HGFile;

/**
 * Setting
 */
class Setting
{
  use ORMBehaviors\Translatable\Translatable;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $options;

    /**
     * @var boolean
     */
    private $culture_aware;

    /**
     * @var string
     */
    private $general_value;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Setting
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Setting
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set options
     *
     * @param string $options
     * @return Setting
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set culture_aware
     *
     * @param boolean $cultureAware
     * @return Setting
     */
    public function setCultureAware($cultureAware)
    {
        $this->culture_aware = $cultureAware;

        return $this;
    }

    /**
     * Get culture_aware
     *
     * @return boolean
     */
    public function getCultureAware()
    {
        return $this->culture_aware;
    }

    /**
     * Set general_value
     *
     * @param string $generalValue
     * @return Setting
     */
    public function setGeneralValue($generalValue)
    {
        $this->general_value = $generalValue;

        return $this;
    }

    /**
     * Get general_value
     *
     * @return string
     */
    public function getGeneralValue()
    {
        return $this->general_value;
    }


  /**
  * Beállítja a kapott paramétert a setting értékének
  *
  * @param mixed $value
  * @param string|null $culture - ha nem nyelvfüggő, akkor a general_value-t állítja, egyébként ezen nyelvű i18n-es rekord value-ját
  */
  public function setSettingValue($value, $culture = null)
  {
    if ($this->getType() == 'file' && $value instanceof FileRepositoryUploadRequestInterface)
    {
      $value = $value->process(true);
      $value = $value instanceof HGFile ? $value->getFilId() : null;
    }
    
    if ($this->getCultureAware())
    {
      $this->setValue($value, $culture);
    }
    else
    {
      $this->setGeneralValue($value);
    }
  }


  /**
  * Visszaadja a paraméterben kapott culture-höz tartozó értéket
  * Ha checkbox típusú, akkor bool konverziót is végez
  *
  * @param string|null $culture - ha nem nyelvfüggő, akkor a general_value-t adja a paramétertől függetlenül, egyébként ezen nyelvű i18n-es rekord value-ját
  * @return mixed - az érték
  */
  public function getValueByCulture($culture = null)
  {
    $value = $this->getCultureAware() ? $this->getValue($culture) : $this->getGeneralValue();

    if (!is_null($value) && $this->getType() == 'checkbox')
    {
      return (bool)$value;
    }

    return $value;
  }

  /**
  * A fileeditable widget opcióit adja tömbben a setting jelenlegi értéke és az átadott culture alapján
  *
  * @param string|null $culture - ha nem nyelvfüggő, akkor a general_value-t használja a paramétertől függetlenül, egyébként ezen nyelvű i18n-es rekord value-ját
  *
  * @return array
  */
  public function getListFileWidgetOptions($culture = null)
  {
    $context = sfContext::getInstance();
    $downloadUrl = $context->getController()->genUrl('@download_setting_file?id='.$this->getId().($culture ? '&culture='.$culture : ''));
    $downloadLabel = $context->getI18N()->__('sf_settings_download_file');
    $repoId = $this->getValueByCulture($culture);
    $template = $repoId ?
      '%input%<br /><a href="'.$downloadUrl.'" target="_blank">'.$downloadLabel.'</a>%delete% %delete_label%' :
      '%input%';

    return array(
            'file_src' => $repoId ? hgabkaFileRepository::getInstance()->getFilePathById($repoId, true) : '',
            'delete_label' => 'sf_settings_remove_file',
            'template' => $template
            );
  }

  public function setValue($value, $culture)
  {
    $this->translate($culture)->setValue($value);
  }

  public function getValue($culture)
  {
    return $this->translate($culture)->getValue();
  }

  public function setValuesByCultures($values)
  {
    foreach ($values as $culture => $value)
    {
      $this->setSettingValue($value, $culture);
    }
  }


 /**
  * Visszaadja, hogy a beállítás fájl típusú-e (akár publikus, akár védett)
  *
  * @return bool - fájl típusú-e
  */
  public function isFileType()
  {
    return in_array($this->getType(), array('file'));
  }

  public function getDefaultLocale()
  {
    return '';
  }

  public function getDescription($locale = null)
  {
    return $this->translate($locale)->getDescription();
  }


}