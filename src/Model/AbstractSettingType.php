<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class AbstractSettingType.
 */
abstract class AbstractSettingType implements SettingTypeInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'hg_settings.types.' . $this->getId();
    }

    public function getPriority(): ?int
    {
        return null;
    }

    /**
     * @return array
     */
    public function getFormTypeOptions(): array
    {
        return [];
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function transformValue(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function reverseTransformValue(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getHtml(mixed $value): mixed
    {
        return $value;
    }

    public function isEditable(): bool
    {
        return true;
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function getFormTransformer(): ?DataTransformerInterface
    {
        return null;
    }
}
