<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\UtilsBundle\Traits\TranslatableTrait;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Setting.
 *
 * @ORM\Table(name="hg_settings_settings")
 * @ORM\Entity(repositoryClass="Hgabka\SettingsBundle\Repository\SettingRepository")
 * @UniqueEntity(fields={"name"}, message="A megadott névvel már létezik beállítás", errorPath="name")
 */
class Setting implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(name="editable", type="boolean", nullable=false)
     */
    protected $editable = true;

    /**
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    protected $visible = true;
    /**
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    protected $required = true;

    /**
     * @ORM\Column(name="culture_aware", type="boolean", nullable=false)
     */
    protected $cultureAware = false;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(name="general_value", type="text", nullable=true)
     */
    protected $generalValue;

    /**
     * @Prezent\Translations(targetEntity="Hgabka\SettingsBundle\Entity\SettingTranslation")
     */
    private $translations;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ?: '';
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Setting
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return Setting
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return Setting
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getValue($locale = null)
    {
        return $this->translate($locale)->getValue();
    }

    public static function getTranslationEntityClass()
    {
        return SettingTranslation::class;
    }

    /**
     * Visszaadja a paraméterben kapott culture-höz tartozó értéket
     * Ha checkbox típusú, akkor bool konverziót is végez.
     *
     * @param null|string $culture - ha nem nyelvfüggő, akkor a general_value-t adja a paramétertől függetlenül, egyébként ezen nyelvű i18n-es rekord value-ját
     * @param null|mixed  $locale
     *
     * @return mixed - az érték
     */
    public function getValueByLocale($locale = null)
    {
        $value = $this->isCultureAware() ? $this->getValue($locale) : $this->getGeneralValue();

        return $value;
    }

    /**
     * @return mixed
     */
    public function isCultureAware()
    {
        return $this->cultureAware;
    }

    /**
     * @param mixed $cultureAware
     *
     * @return Setting
     */
    public function setCultureAware($cultureAware)
    {
        $this->cultureAware = $cultureAware;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeneralValue()
    {
        return $this->generalValue;
    }

    /**
     * @param mixed $generalValue
     *
     * @return Setting
     */
    public function setGeneralValue($generalValue)
    {
        $this->generalValue = $generalValue;

        return $this;
    }

    public function getDescription($locale = null)
    {
        return $this->translate($locale)->getDescription();
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * @param mixed $editable
     *
     * @return Setting
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param mixed $visible
     *
     * @return Setting
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param mixed $required
     *
     * @return Setting
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }
}
