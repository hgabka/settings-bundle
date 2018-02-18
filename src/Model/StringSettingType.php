<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class StringSettingType.
 */
class StringSettingType extends AbstractSettingType
{
    /**
     * @return mixed|string
     */
    public function getId()
    {
        return 'string';
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
        return TextType::class;
    }

    /**
     * @return array
     */
    public function getFormTypeOptions()
    {
        return ['attr' => [
           'class' => 'form-control',
        ]];
    }
}
