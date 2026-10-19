<?php

namespace Hgabka\SettingsBundle\Validator;

use Hgabka\SettingsBundle\Helper\SettingsManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SettingValidator extends ConstraintValidator
{
    /**
     * @var SettingsManager
     */
    private $manager;

    public function __construct(SettingsManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param mixed              $value
     * @param Constraint|Setting $constraint
     *
     * @throws \Exception
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $val = $this->manager->get($constraint->name);
        switch (strtoupper($constraint->comparator)) {
            case '=':
            case 'EQUALS':
                $check = $value === $val;

                break;
            case '>':
            case 'GREATER':
                $check = $value > $val;

                break;
            case '<':
            case 'LESSER':
                $check = $value < $val;

                break;
            default:
                throw new \Exception('Invalid comparator');
        }
        if (!$check) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setParameter('{{ comparator }}', $constraint->comparator)
                ->addViolation()
            ;
        }
    }
}
