<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\SettingsBundle\Repository\SettingCategoryRepository;
use Hgabka\UtilsBundle\Traits\TranslatableTrait;

#[ORM\Entity(repositoryClass: SettingCategoryRepository::class)]
#[ORM\Table(name: 'hg_settings_setting_category')]
class SettingCategory implements TranslatableInterface
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'position', type: 'integer')]
    #[Gedmo\SortablePosition]
    private ?int $position = null;

    /**
     * @Hgabka\Translations(targetEntity="Hgabka\SettingsBundle\Entity\SettingCategoryTranslation")
     */
    #[Hgabka\Translations(targetEntity: SettingCategoryTranslation::class)]
    private Collection|array|null $translations;

    #[ORM\OneToMany(targetEntity: Setting::class, mappedBy: 'category')]
    private Collection|array|null $settings;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->settings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getName(?string $locale = null): ?string
    {
        return $this->translate($locale)->getName();
    }

    public static function getTranslationEntityClass(): string
    {
        return SettingCategoryTranslation::class;
    }

    public function getSettings(): Collection|array|null
    {
        return $this->settings;
    }

    public function addSetting(Setting $setting): self
    {
        if (!$this->settings->contains($setting)) {
            $this->settings[] = $setting;
            $setting->setCategory($this);
        }

        return $this;
    }

    public function removeSetting(Setting $setting): self
    {
        if ($this->settings->removeElement($setting)) {
            if ($setting->getCategory() === $this) {
                $setting->setCategory(null);
            }
        }

        return $this;
    }
}
