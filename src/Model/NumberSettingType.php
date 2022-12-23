<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\NumberType;

class NumberSettingType extends AbstractSettingType
{
    /**
     * @return mixed|string
     */
    public function getId()
    {
        return 'number';
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

    /**
     * @param $value
     *
     * @return bool|mixed
     */
    public function transformValue($value)
    {
        return (float) $value;
    }

    /**
     * @param $value
     *
     * @return bool|mixed
     */
    public function reverseTransformValue($value)
    {
        return (float) $value;
    }
}
