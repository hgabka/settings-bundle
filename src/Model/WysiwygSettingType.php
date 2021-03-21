<?php

namespace Hgabka\SettingsBundle\Model;

use Hgabka\UtilsBundle\Form\WysiwygType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
