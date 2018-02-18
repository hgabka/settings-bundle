<?php

namespace Hgabka\SettingsBundle\EventListener;

use Hgabka\SettingsBundle\Event\SettingFormTypeEvent;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Hgabka\UtilsBundle\Form\Type\StaticControlType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BaseSettingFormTypeSubscriber implements EventSubscriberInterface
{
    /** @var SettingsManager */
    protected $settingsManager;

    /**
     * SpecialSettingFormTypeSubscriber constructor.
     *
     * @param SettingsManager $settingsManager
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            SettingFormTypeEvent::EVENT_FORM_ADD => ['onFormAdd', PHP_INT_MAX],
        ];
    }

    public function onFormAdd(SettingFormTypeEvent $event)
    {
        $setting = $event->getSetting();
        $type = $this->settingsManager->getType($setting->getType());

        if (!$type || !$setting->isVisible()) {
            $setting->setFormType(null);
            $event->stopPropagation();

            return;
        }

        $formType = $type->getFormType();
        $options = $type->getFormTypeOptions();

        if (!$setting->isEditable()) {
            $formType = StaticControlType::class;
            $options = ['attr' => [
                    'class' => 'form-control',
                ]
            ];
        } else {
            if (!$setting->isRequired()) {
                $options['required'] = false;
                $this->settingsManager->removeConstraint($options, new NotBlank());
            } else {
                $options['required'] = true;
                $this->settingsManager->addConstraints($options, new NotBlank());
            }
        }

        $setting
            ->setFormType($formType)
            ->setFormOptions($options);
        ;

    }
}