<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\NumberType;

class NumberSettingType extends AbstractSettingType
{
    /**
     * @return mixed|string
     */
    public function getId(): string
    {
        return 'number';
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
        return NumberType::class;
    }

    /**
     * @return array
     */
    public function getFormTypeOptions(): array
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
    public function transformValue(mixed $value): float
    {
        return (float) $value;
    }

    /**
     * @param $value
     *
     * @return bool|mixed
     */
    public function reverseTransformValue(mixed $value): float
    {
        return (float) $value;
    }
}
