<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class IntegerSettingType extends AbstractSettingType
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
        return IntegerType::class;
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
        return (int) $value;
    }

    /**
     * @param $value
     *
     * @return bool|mixed
     */
    public function reverseTransformValue($value)
    {
        return (int) $value;
    }
}
