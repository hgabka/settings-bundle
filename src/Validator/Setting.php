<?php

namespace Hgabka\SettingsBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Setting extends Constraint
{
    public $name;
    public $comparator = '=';
    public function validatedBy()
    {
        return SettingValidator::class;
    }
}