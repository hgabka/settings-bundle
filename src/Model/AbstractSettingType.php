<?php

namespace Hgabka\SettingsBundle\Model;

/**
 * Class AbstractSettingType.
 */
abstract class AbstractSettingType implements SettingTypeInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'hg_settings.types.'.$this->getId();
    }

    public function getPriority()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getFormTypeOptions()
    {
        return [];
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function transformValue($value)
    {
        return $value;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function reverseTransformValue($value)
    {
        return $value;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getHtml($value)
    {
        return $value;
    }

    public function isEditable()
    {
        return true;
    }

    public function isVisible()
    {
        return true;
    }
}
