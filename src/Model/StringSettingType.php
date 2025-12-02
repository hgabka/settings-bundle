<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class StringSettingType.
 */
class StringSettingType extends AbstractSettingType
{
    /**
     * @return mixed|string
     */
    public function getId(): string
    {
        return 'string';
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
        return TextType::class;
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
}
