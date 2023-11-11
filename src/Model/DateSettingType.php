<?php

namespace Hgabka\SettingsBundle\Model;

use Sonata\Form\Type\DatePickerType;

class DateSettingType extends DateTimeSettingType
{
    public function getId(): string
    {
        return 'date';
    }

    public function getFormType(): string
    {
        return DatePickerType::class;
    }

    public function getFormTypeOptions(): array
    {
        return ['format' => 'yyyy. MMMM d.'];
    }

    public function transformValue(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return $value->format('Y-m-d');
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function reverseTransformValue(mixed $value): mixed
    {
        if (empty($value)) {
            return $value;
        }

        return \DateTime::createFromFormat('Y-m-d', $value);
    }
}
