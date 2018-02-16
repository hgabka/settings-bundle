<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\SettingsBundle\Enum\SettingTypes;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Setting.
 *
 * @ORM\Table(name="hg_settings_settings")
 * @ORM\Entity(repositoryClass="Hgabka\SettingsBundle\Repository\SettingRepository")
 */
class Setting
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $description;

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
}