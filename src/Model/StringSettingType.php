<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class StringSettingType extends AbstractSettingType
{
    public function getId()
    {
        return 'string';
    }

    public function getPriority()
    {
        return 1;
    }

    public function getFormType()
    {
        return TextType::class;
    }

    public function getFormTypeOptions()
    {
        return ['attr' => [
           'class' => 'form-control',
        ]];
    }
}
