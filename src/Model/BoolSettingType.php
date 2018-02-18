<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class BoolSettingType extends AbstractSettingType
{
    public function getId()
    {
        return 'bool';
    }

    public function getPriority()
    {
        return 3;
    }

    public function getFormType()
    {
        return CheckboxType::class;
    }

    public function transformValue($value)
    {
        return (bool) $value;
    }

    public function reverseTransformValue($value)
    {
        return (bool) $value;
    }
}
