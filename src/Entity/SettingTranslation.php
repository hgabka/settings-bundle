<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\Entity\TranslationTrait;
use Hgabka\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Table(name="hg_settings_settings_translation")
 * @ORM\Entity
 */
class SettingTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @Hgabka\Translatable(targetEntity="Hgabka\SettingsBundle\Entity\Setting")
     */
    private $translatable;

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
