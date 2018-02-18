<?php

namespace Hgabka\SettingsBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Event\SettingFormTypeEvent;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Hgabka\UtilsBundle\Form\Type\StaticControlType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingsType extends AbstractType
{
    private $settings;

    private $locales;

    private $types;

    /** @var SettingsManager $manager */
    private $manager;

    /** @var EventDispatcherInterface $dispatcher */
    private $dispatcher;

    public function __construct(SettingsManager $settingsManager, ManagerRegistry $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->settings = $entityManager->getRepository(Setting::class)->findAll();
        $this->locales = $settingsManager->getLocales();
        $this->types = $settingsManager->getTypes();
        $this->manager = $settingsManager;
        $this->dispatcher = $dispatcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->settings as $setting) {
            if (!$setting->isVisible()) {
                continue;
            }

            $type = $this->manager->getType($setting->getType());

            $event = new SettingFormTypeEvent($setting);
            $this->dispatcher->dispatch(SettingFormTypeEvent::EVENT_FORM_ADD, $event);

            $formType = $setting->getFormType();
            if (empty($formType)) {
                continue;
            }
            $options = $setting->getFormOptions();

            if (!$setting->isCultureAware()) {
                $options['label'] = false;
                //    var_dump($setting->getGeneralValue()); die();
                $options['data'] = $type->reverseTransformValue($setting->getGeneralValue());
                if (!$setting->isEditable()) {
                    $options['html'] = $type->getHtml($options['data']);
                }
                $builder->add(
                    $builder->create($setting->getId(), FormType::class, ['label' => false])
                            ->add('general_value', $formType, $options)
                );
            } else {
                $oneForm = $builder->create($setting->getId(), FormType::class, ['label' => false]);
                foreach ($this->locales as $culture) {
                    $options['label'] = 'hg_settings.label.'.$culture;
                    $options['data'] = $type->reverseTransformValue($setting->getValue($culture));
                    if (!$setting->isEditable()) {
                        $options['html'] = $type->getHtml($options['data']);
                    }

                    $oneForm
                            ->add($culture, $formType, $options);
                }
                $builder->add($oneForm);
            }
        }
    }

    public function getBlockPrefix()
    {
        return 'settings';
    }
}
