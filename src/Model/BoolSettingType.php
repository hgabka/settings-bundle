<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class BoolSettingType.
 */
class BoolSettingType extends AbstractSettingType
{
    /**
     * @return string
     */
    public function getId(): string
    {
        return 'bool';
    }

    /**
     * @return null|int
     */
    public function getPriority(): ?int
    {
        return 3;
    }

    /**
     * @return string
     */
    public function getFormType(): string
    {
        return CheckboxType::class;
    }

    /**
     * @param $value
     *
     * @return bool|mixed
     */
    public function transformValue(mixed $value): bool
    {
        return (bool) $value;
    }

    /**
     * @param $value
     *
     * @return bool|mixed
     */
    public function reverseTransformValue(mixed $value): bool
    {
        return (bool) $value;
    }
}
