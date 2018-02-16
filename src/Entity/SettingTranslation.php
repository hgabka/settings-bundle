<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\TranslationTrait;
use Prezent\Doctrine\Translatable\TranslationInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="hg_settings_settings_translation")
 * @ORM\Entity
 */
class SettingTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @Prezent\Translatable(targetEntity="Hgabka\SettingsBundle\Entity\Setting")
     */
    private $translatable;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $description;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
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
     * @param mixed $name
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
     * @return SettingTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
