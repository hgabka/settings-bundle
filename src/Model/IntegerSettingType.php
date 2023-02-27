<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class IntegerSettingType extends AbstractSettingType
{
    /**
     * @return mixed|string
     */
    public function getId(): string
    {
        return 'integer';
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
        return IntegerType::class;
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
    public function transformValue(mixed $value): int
    {
        return (int) $value;
    }

    /**
     * @param $value
     *
     * @return bool|mixed
     */
    public function reverseTransformValue(mixed $value): int
    {
        return (int) $value;
    }
}
