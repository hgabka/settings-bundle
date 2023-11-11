<?php

namespace Hgabka\SettingsBundle\Model;

use Sonata\Form\Type\DateTimePickerType;

class DateTimeSettingType extends AbstractSettingType
{
    public function getId(): string
    {
        return 'datetime';
    }

    /**
     * @return null|int
     */
    public function getPriority(): ?int
    {
        return 1;
    }

    /**
     * @return mixed|string
     */
    public function getFormType(): string
    {
        return DateTimePickerType::class;
    }

    public function getFormTypeOptions(): array
    {
        return ['format' => 'yyyy. MMMM d. HH:mm'];
    }

    public function transformValue(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return $value->format('Y-m-d H:i');
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

        return \DateTime::createFromFormat('Y-m-d H:i', $value);
    }
}
