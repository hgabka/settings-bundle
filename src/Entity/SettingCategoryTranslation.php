<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\Entity\TranslationTrait;
use Hgabka\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Table(name="hg_settings_setting_category_translation")
 * @ORM\Entity
 */
class SettingCategoryTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var null|SettingCategory
     *
     * @Hgabka\Translatable(targetEntity="Hgabka\SettingsBundle\Entity\SettingCategory")
     */
    private $translatable;

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return SettingCategoryTranslation
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getTranslatable(): ?SettingCategory
    {
        return $this->translatable;
    }
}
