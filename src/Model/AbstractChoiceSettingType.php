<?php

namespace Hgabka\SettingsBundle\Model;

use function array_merge;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

abstract class AbstractChoiceSettingType extends AbstractSettingType
{
    protected $choiceLoaderClass = null;

    protected $choices = null;

    public function getFormType(): string
    {
        return ChoiceType::class;
    }

    public function getFormTypeOptions(): array
    {
        $options = [];
        if (!empty($this->choiceLoaderClass)) {
            $options['choice_loader'] = new $this->choiceLoaderClass();
        } elseif (!empty($this->choices)) {
            $options['choices'] = $this->choices;
        }

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
