<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class BoolSettingType.
 */
class BoolSettingType extends AbstractSettingType
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'bool';
    }

    /**
     * @return null|int
     */
    public function getPriority()
    {
        return 3;
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return CheckboxType::class;
    }

    /**
     * @param $value
     *
     * @return bool|mixed
     */
    public function transformValue($value)
    {
        return (bool) $value;
    }

    /**
     * @param $value
     *
     * @return bool|mixed
     */
    public function reverseTransformValue($value)
    {
        return (bool) $value;
    }
}
