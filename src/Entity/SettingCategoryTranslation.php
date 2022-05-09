<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\Entity\TranslationTrait;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\Doctrine\Translatable\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: 'hg_settings_setting_category_translation')]
class SettingCategoryTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    protected ?string $name = null;

    /**
     * @var null|SettingCategory
     *
     * @Hgabka\Translatable(targetEntity="Hgabka\SettingsBundle\Entity\SettingCategory")
     */
    #[Hgabka\Translatable(targetEntity: SettingCategory::class)]
    private ?TranslatableInterface $translatable = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTranslatable(): ?SettingCategory
    {
        return $this->translatable;
    }
}
