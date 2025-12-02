<?php

namespace Hgabka\SettingsBundle\Model;

use Hgabka\UtilsBundle\Form\WysiwygType;

class WysiwygSettingType extends AbstractSettingType
{
    /**
     * @return string
     */
    public function getId(): string
    {
        return 'wysiwyg';
    }

    /**
     * @return null|int
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * @return mixed|string
     */
    public function getFormType(): string
    {
        return WysiwygType::class;
    }
}
