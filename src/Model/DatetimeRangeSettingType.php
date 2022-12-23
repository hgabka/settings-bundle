<?php

namespace App\Setting;

use Hgabka\SettingsBundle\Model\AbstractSettingType;
use Sonata\Form\Type\DateTimeRangePickerType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class DatetimeRangeSettingType extends AbstractSettingType
{
    /**
     * @return mixed|string
     */
    public function getId(): string
    {
        return 'datetimerange';
    }

    public function getName(): string
    {
        return 'Időintervallum';
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
        return DateTimeRangePickerType::class;
    }

    /**
     * @return array
     */
    public function getFormTypeOptions(): array
    {
        return [
            'field_options_start' => [
                'format' => 'yyyy. MMMM d. HH:mm',
            ],
            'field_options_end' => [
                'format' => 'yyyy. MMMM d. HH:mm',
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

    /**
     * @param $value
     *
     * @return mixed
     */
    public function transformValue($value)
    {
        if (empty($value)) {
            return null;
        }

        return serialize($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function reverseTransformValue($value)
    {
        if (empty($value)) {
            return $value;
        }

        return unserialize($value);
    }

    public function getFormTransformer(): ?DataTransformerInterface
    {
        return new CallbackTransformer(
            function ($value) {
                if (empty($value)) {
                    return $value;
                }

                return [
                'start' => $value['from'] ?? null,
                'end' => $value['to'] ?? null,
            ];
            },
            function ($value) {
                if (empty($value)) {
                    return null;
                }

                return [
                    'from' => $value['start'] ?? null,
                    'to' => $value['end'] ?? null,
                ];
            }
        );
    }
}
