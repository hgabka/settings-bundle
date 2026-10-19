<?php

namespace Hgabka\SettingsBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Setting extends Constraint
{
    public $name;
    public $comparator = '=';
    public $message = 'The value {{ value }} does not satisfy the "{{ comparator }}" comparison with the setting.';

    public function validatedBy(): string
    {
        return SettingValidator::class;
    }
}
