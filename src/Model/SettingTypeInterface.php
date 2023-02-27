<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Interface SettingTypeInterface.
 */
interface SettingTypeInterface
{
    /**
     * @return mixed
     */
    public function getId(): string;

    /**
     * @return mixed
     */
    public function getName(): string;

    /**
     * @return mixed
     */
    public function getPriority(): ?int;

    /**
     * @return mixed
     */
    public function getFormType(): string;

    /**
     * @return mixed
     */
    public function getFormTypeOptions(): ?array;

    /**
     * @param $value
     *
     * @return mixed
     */
    public function transformValue(mixed $value): mixed;

    /**
     * @param $value
     *
     * @return mixed
     */
    public function reverseTransformValue(mixed $value): mixed;

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getHtml(mixed $value): mixed;

    /**
     * @return bool
     */
    public function isEditable(): bool;

    /**
     * @return bool
     */
    public function isVisible(): bool;

    public function getFormTransformer(): ?DataTransformerInterface;
}
