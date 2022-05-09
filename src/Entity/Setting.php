<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\SettingsBundle\Repository\SettingRepository;
use Hgabka\UtilsBundle\Traits\TranslatableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
#[ORM\Table(name: 'hg_settings_settings')]
#[UniqueEntity(fields: ['name'], message: 'A megadott névvel már létezik beállítás', errorPath: 'name')]
class Setting implements TranslatableInterface
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: SettingCategory::class, inversedBy: 'settings')]
    #[ORM\JoinColumn(onDelete: 'SET NULL', nullable: true)]
    protected ?SettingCategory $category = null;

    #[ORM\Column(name: 'name', type: 'string', nullable: false)]
    protected ?string $name = null;

    #[ORM\Column(name: 'editable', type: 'boolean', nullable: false)]
    protected bool $editable = true;

    #[ORM\Column(name: 'visible', type: 'boolean', nullable: false)]
    protected bool $visible = true;

    #[ORM\Column(name: 'required', type: 'boolean', nullable: false)]
    protected bool $required = true;

    #[ORM\Column(name: 'culture_aware', type: 'boolean', nullable: false)]
    protected bool $cultureAware = false;

    #[ORM\Column(name: 'type', type: 'string', length: 50, nullable: false)]
    protected ?string $type = null;

    #[ORM\Column(name: 'general_value', type: 'text', nullable: true)]
    protected ?string $generalValue = null;

    /**
     * @Hgabka\Translations(targetEntity="Hgabka\SettingsBundle\Entity\SettingTranslation")
     */
    #[Hgabka\Translations(targetEntity: SettingTranslation::class)]
    private Collection|array|null $translations;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName() ?: '';
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

    public function getCategory(): ?SettingCategory
    {
        return $this->category;
    }

    public function setCategory(?SettingCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(?string $locale = null): ?string
    {
        return $this->translate($locale)->getValue();
    }

    public static function getTranslationEntityClass(): string
    {
        return SettingTranslation::class;
    }

    /**
     * Visszaadja a paraméterben kapott culture-höz tartozó értéket
     *
     * @param null|string $culture - ha nem nyelvfüggő, akkor a general_value-t adja a paramétertől függetlenül, egyébként ezen nyelvű i18n-es rekord value-ját
     * @param null|mixed  $locale
     *
     * @return string - az érték
     */
    public function getValueByLocale(?string $locale = null): ?string
    {
        $value = $this->isCultureAware() ? $this->getValue($locale) : $this->getGeneralValue();

        return $value;
    }

    public function isCultureAware(): bool
    {
        return $this->cultureAware;
    }

    public function setCultureAware(bool $cultureAware): self
    {
        $this->cultureAware = $cultureAware;

        return $this;
    }

    public function getGeneralValue(): ?string
    {
        return $this->generalValue;
    }

    public function setGeneralValue(?string $generalValue): self
    {
        $this->generalValue = $generalValue;

        return $this;
    }

    public function getDescription(?string $locale = null): ?string
    {
        return $this->translate($locale)->getDescription();
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }

    public function setEditable(bool $editable): self
    {
        $this->editable = $editable;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }
}
