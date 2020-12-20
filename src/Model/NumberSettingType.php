<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class NumberSettingType extends AbstractSettingType
{
    /**
     * @return mixed|string
     */
    public function getId()
    {
        return 'integer';
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
        return NumberType::class;
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