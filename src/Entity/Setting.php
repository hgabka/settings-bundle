<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\SettingsBundle\Enum\SettingTypes;
use Symfony\Component\Validator\Constraints as Assert;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Hgabka\UtilsBundle\Traits\TranslatableTrait;
use Hgabka\SettingsBundle\Entity\SettingTranslation;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(name="culture_aware", type="boolean", nullable=false)
     */
    protected $cultureAware;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(name="general_value", type="text", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Type(type="integer", groups={"TypeInt"})
     * @Assert\Type(type="string", groups={"TypeStr"})
     * @Assert\Type(type="float", groups={"TypeFloat"})
     * @Assert\Choice(choices={0,1}, groups={"TypeBool"})
     * @Assert\Email(groups={"TypeEmail"})
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

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Type(type="integer", groups={"TypeInt"})
     * @Assert\Type(type="string", groups={"TypeStr"})
     * @Assert\Type(type="float", groups={"TypeFloat"})
     * @Assert\Choice(choices={0,1}, groups={"TypeBool"})
     * @Assert\Email(groups={"TypeEmail"})
     */
    protected $value;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Setting
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    public function __toString()
    {
        return $this->getName();
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
     * @param mixed $value
     *
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return Setting
     */
    public function setDescription($description)
    {
        $this->description = $description;

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

    /**
     * The converted value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getValueConverted()
    {
        $val = $this->getValue();
        if (null !== $val) {
            switch ($this->getType()) {
                case SettingTypes::INT:
                    $val = (int)$val;
                    break;
                case SettingTypes::BOOL:
                    $val = (bool)$val;
                    break;
                case SettingTypes::FLOAT:
                    $val = (float)$val;
                    break;
                default:
                    $val = (string)$val;
            }
        }

        return $val;
    }

    public static function getTranslationEntityClass()
    {
        return SettingTranslation::class;
    }

    /**
     * @return mixed
     */
    public function getCultureAware()
    {
        return $this->cultureAware;
    }

    /**
     * @param mixed $cultureAware
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
     * @return Setting
     */
    public function setGeneralValue($generalValue)
    {
        $this->generalValue = $generalValue;

        return $this;
    }
}