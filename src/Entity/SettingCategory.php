<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\UtilsBundle\Traits\TranslatableTrait;

/**
 * Setting.
 *
 * @ORM\Table(name="hg_settings_setting_category")
 * @ORM\Entity(repositoryClass="Hgabka\SettingsBundle\Repository\SettingCategoryRepository")
 */
class SettingCategory implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var null|int
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @Hgabka\Translations(targetEntity="Hgabka\SettingsBundle\Entity\SettingCategoryTranslation")
     */
    private $translations;

    /**
     * @ORM\OneToMany(targetEntity=Setting::class, mappedBy="category")
     */
    private $settings;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->settings = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return SettingCategory
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return SettingCategory
     */
    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getName($locale = null)
    {
        return $this->translate($locale)->getName();
    }

    public static function getTranslationEntityClass()
    {
        return SettingCategoryTranslation::class;
    }

    /**
     * @return Collection|CouponLog[]
     */
    public function getSettings(): Collection
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
            // set the owning side to null (unless already changed)
            if ($setting->getCategory() === $this) {
                $setting->setCategory(null);
            }
        }

        return $this;
    }
}
