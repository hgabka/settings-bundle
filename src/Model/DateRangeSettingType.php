<?php

namespace Hgabka\SettingsBundle\Model;

use Sonata\Form\Type\DateRangePickerType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class DateRangeSettingType extends DatetimeRangeSettingType
{
    /**
     * @return mixed|string
     */
    public function getId(): string
    {
        return 'daterange';
    }

    /**
     * @return null|int
     */
    public function getPriority(): ?int
    {
        return 1;
    }

    /**
     * @return mixed|string
     */
    public function getFormType(): string
    {
        return DateRangePickerType::class;
    }

    /**
     * @return array
     */
    public function getFormTypeOptions(): array
    {
        return [
            'field_options_start' => [
                'format' => 'yyyy. MMMM d.',
            ],
            'field_options_end' => [
                'format' => 'yyyy. MMMM d.',
            ],
            'error_bubbling' => false,
            'constraints' => [
                new Callback([
                    'callback' => static function (array $values, ExecutionContextInterface $context) {
                        if (empty($values['from']) || empty($values['to'])) {
                            return;
                        }

                        if ($values['from'] > $values['to']) {
                            $context
                                ->buildViolation('A kezdődátum nem lehet korábbi a végdátumnál')
                                ->atPath('[general_value]')
                                ->addViolation()
                            ;
                        }
                    },
                ]),
            ],
        ];
    }
}
