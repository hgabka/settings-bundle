<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextSettingType extends AbstractSettingType
{
    /**
     * @return mixed|string
     */
    public function getId(): string
    {
        return 'text';
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
        return TextareaType::class;
    }

    /**
     * @return array
     */
    public function getFormTypeOptions(): array
    {
        return ['attr' => [
            'class' => 'form-control',
            'rows' => 10,
        ]];
    }
}
