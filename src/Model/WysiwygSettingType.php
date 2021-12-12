<?php

namespace Hgabka\SettingsBundle\Model;

use Hgabka\UtilsBundle\Form\WysiwygType;

class WysiwygSettingType extends AbstractSettingType
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'wysiwyg';
    }

    /**
     * @return null|int
     */
    public function getPriority()
    {
        return 1;
    }

    /**
     * @return mixed|string
     */
    public function getFormType()
    {
        return WysiwygType::class;
    }
}
