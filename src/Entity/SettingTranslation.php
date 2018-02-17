<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\TranslationTrait;
use Prezent\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Table(name="hg_settings_settings_translation")
 * @ORM\Entity
 */
class SettingTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @Prezent\Translatable(targetEntity="Hgabka\SettingsBundle\Entity\Setting")
     */
    private $translatable;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return SettingTranslation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return SettingTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return SettingTranslation
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
