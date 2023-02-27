<?php

namespace Hgabka\SettingsBundle\Model;

use Hgabka\UtilsBundle\Form\Type\TranslatedEnumType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AbstractTranslatedEnumSettingType extends AbstractSettingType
{
    protected ?string $enumClass = null;

    public function getFormType(): string
    {
        return TranslatedEnumType::class;
    }

    public function getFormTypeOptions(): array
    {
        $options = [
            'class' => $this->enumClass,
        ];

        if (!empty(($choiceOptions = $this->getChoiceOptions()))) {
            $options = array_merge($options, $choiceOptions);
        }

        return $options;
    }

    protected function getChoiceOptions(): array
    {
        return [
            'placeholder' => '',
        ];
    }
}
