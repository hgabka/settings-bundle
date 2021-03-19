<?php

namespace Hgabka\SettingsBundle\EventListener;

use Hgabka\SettingsBundle\Event\SettingFormTypeEvent;
use Hgabka\UtilsBundle\Form\Type\StaticControlType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class GeneralSettingFormTypeSubscriber.
 */
class GeneralSettingFormTypeSubscriber extends BaseSettingFormTypeSubscriber
{
    /** @var AuthorizationCheckerInterface */
    protected $authChecker;

    /** @var string */
    protected $creatorRole;

    public function setAuthChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authChecker = $authorizationChecker;
    }

    /**
     * @param $creatorRole
     */
    public function setCreatorRole($creatorRole)
    {
        $this->creatorRole = $creatorRole;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SettingFormTypeEvent::EVENT_FORM_ADD => ['onFormAdd', \PHP_INT_MAX],
        ];
    }

    public function onFormAdd(SettingFormTypeEvent $event)
    {
        $setting = $event->getSetting();
        $type = $this->settingsManager->getType($setting->getType());

        if (!$type || !$type->isVisible() || (!$setting->isVisible() && !$this->authChecker->isGranted($this->creatorRole))) {
            $event->setFormBuilder(null);
            $event->stopPropagation();

            return;
        }

        $formType = $type->getFormType();
        $options = $type->getFormTypeOptions();
        $oneForm = $event->getFormBuilder();

        if (!$setting->isEditable() || !$type->isEditable()) {
            $formType = StaticControlType::class;
            $options = ['attr' => [
                    'class' => 'form-control',
                ],
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

        if (!$setting->isCultureAware()) {
            $options['label'] = false;
            $options['data'] = $type->reverseTransformValue($setting->getGeneralValue());
            if (!$setting->isEditable() || !$type->isEditable()) {
                $options['html'] = $type->getHtml($options['data']);
            }
            $oneForm->add('general_value', $formType, $options);
            if (!empty($type->getFormTransformer())) {
                $oneForm->get('general_value')->addModelTransformer($type->getFormTransformer());
            }
        } else {
            foreach ($this->settingsManager->getLocales() as $culture) {
                $options['label'] = 'hg_settings.label.'.$culture;
                $options['data'] = $type->reverseTransformValue($setting->getValue($culture));
                if (!$setting->isEditable() || !$type->isEditable()) {
                    $options['html'] = $type->getHtml($options['data']);
                }

                $oneForm->add($culture, $formType, $options);
                if (!empty($type->getFormTransformer())) {
                    $oneForm->get($culture)->addModelTransformer($type->getFormTransformer());
                }                
            }
        }
    }
}
