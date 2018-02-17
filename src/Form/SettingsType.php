<?php

namespace Hgabka\SettingsBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Hgabka\UtilsBundle\Form\Type\StaticControlType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingsType extends AbstractType
{
    private $settings;

    private $locales;

    private $types;

    private $manager;

    public function __construct(SettingsManager $settingsManager, ManagerRegistry $entityManager)
    {
        $this->settings = $entityManager->getRepository(Setting::class)->findAll();
        $this->locales = $settingsManager->getLocales();
        $this->types = $settingsManager->getTypes();
        $this->manager = $settingsManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->settings as $setting) {
            if (!$setting->isVisible()) {
                continue;
            }

            $type = $this->manager->getType($setting->getType());
            $options = ['required' => false];
            if (!$type) {
                continue;
            }
            $formType = $setting->isEditable() ? $type->getFormType() : StaticControlType::class;
            $formTypeOptions = $type->getFormTypeOptions();
            if (!$setting->isRequired()) {
                unset($formTypeOptions['required']);
                $options['required'] = false;
            } else {
                $options['constraints'] = $formTypeOptions['constraints'] ?? [];
                $options['constraints'][] = new NotBlank();
                $options['required'] = true;
                unset($formTypeOptions['constraints']);
            }

            if (!$setting->isCultureAware()) {
                $options['label'] = false;
                //    var_dump($setting->getGeneralValue()); die();
                $options['data'] = $type->reverseTransformValue($setting->getGeneralValue());
                if (!$setting->isEditable()) {
                    $options['html'] = $type->getHtml($options['data']);
                }
                $builder->add(
                    $builder->create($setting->getId(), FormType::class, ['label' => false])
                            ->add('general_value', $formType, array_merge($formTypeOptions, $options))
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
                            ->add($culture, $formType, array_merge($formTypeOptions, $options));
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
