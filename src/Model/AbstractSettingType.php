<?php

namespace Hgabka\SettingsBundle\Model;

abstract class AbstractSettingType implements SettingTypeInterface
{
    public function getName()
    {
        return 'hg_settings.types.'.$this->getId();
    }

    public function getPriority()
    {
        return null;
    }

    public function getFormTypeOptions()
    {
        return [];
    }

    public function transformValue($value)
    {
        return $value;
    }

    public function reverseTransformValue($value)
    {
        return $value;
    }

    public function getHtml($value)
    {
        return $value;
    }
}
