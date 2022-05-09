<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\Entity\TranslationTrait;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\Doctrine\Translatable\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: 'hg_settings_settings_translation')]
class SettingTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[ORM\Column(name: 'description', type: 'string', nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(name: 'value', type: 'text', nullable: true)]
    protected ?string $value = null;

    /**
     * @var null|Setting
     *
     * @Hgabka\Translatable(targetEntity="Hgabka\SettingsBundle\Entity\Setting")
     */
    #[Hgabka\Translatable(targetEntity: Setting::class)]
    private ?TranslatableInterface $translatable = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getTranslatable(): ?Setting
    {
        return $this->translatable;
    }
}
