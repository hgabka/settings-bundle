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
    public function getId();

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getPriority();

    /**
     * @return mixed
     */
    public function getFormType();

    /**
     * @return mixed
     */
    public function getFormTypeOptions();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function transformValue($value);

    /**
     * @param $value
     *
     * @return mixed
     */
    public function reverseTransformValue($value);

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getHtml($value);

    /**
     * @return bool
     */
    public function isEditable();

    /**
     * @return bool
     */
    public function isVisible();
    
    /**
     * @return DataTransformerInterface|null
     */
    public function getFormTransformer(): ?DataTransformerInterface;
}
