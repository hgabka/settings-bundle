<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;

class EmailSettingType extends AbstractSettingType
{
    public function getId()
    {
        return 'email';
    }

    public function getPriority()
    {
        return 2;
    }

    public function getFormType()
    {
        return EmailType::class;
    }

    public function getFormTypeOptions()
    {
        return [
            'constraints' => [
                new Email(),
            ],
            'attr' => [
                'class' => 'form-control',
            ],
        ];
    }
}
