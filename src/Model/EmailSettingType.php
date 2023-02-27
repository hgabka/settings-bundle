<?php

namespace Hgabka\SettingsBundle\Model;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Class EmailSettingType.
 */
class EmailSettingType extends AbstractSettingType
{
    /**
     * @return string
     */
    public function getId(): string
    {
        return 'email';
    }

    /**
     * @return null|int
     */
    public function getPriority(): int
    {
        return 2;
    }

    /**
     * @return string
     */
    public function getFormType(): string
    {
        return EmailType::class;
    }

    /**
     * @return array
     */
    public function getFormTypeOptions(): array
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
