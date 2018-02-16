<?php

namespace Hgabka\SettingsBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
            $options = ['required' => false];
            if (!isset($this->types[$setting->getType()])) {
                continue;
            }
            $type = $this->types[$setting->getType()];
            $widget = $type['form_type'];

            if (isset($type['options']['choice_callback'])) {
                $options['choices'] = $this->manager->{$type['options']['choice_callback']}($setting);
            } elseif (isset($type['options']['choices'])) {
                $options['choices'] = $type['options']['choices'];
            }

            if (isset($type['options']['expanded'])) {
                $options['expanded'] = $type['options']['expanded'];
            }

            if (isset($type['options']['empty_value'])) {
                $options['empty_value'] = $type['options']['empty_value'];
            }

            if (isset($type['options']['width'])) {
                $options['width'] = $type['options']['width'];
            }

            if (!$setting->getCultureAware()) {
                $options['label'] = 'hg_settings_nyelvfuggetlen';
                $options['data'] = $setting->getType() == 'file' ? $this->manager->getFileManager()->getFileObject($setting->getValueByCulture()) : $setting->getValueByCulture();
                if ($setting->getType() == 'file') {
                    $builder->add(
                        $builder->create($setting->getId(), 'form', ['label' => false])
                                ->add('general_value', $widget, array_merge($options, $type['options'])));
                } else {
                    $builder->add(
                        $builder->create($setting->getId(), 'form', ['label' => false])
                                ->add('general_value', $widget, $options));
                }
            } else {
                $oneForm = $builder->create($setting->getId(), 'form', ['label' => false]);
                foreach ($this->locales as $culture) {
                    $options['label'] = 'hg_settings_language_' . $culture;
                    $options['data'] = $setting->getType() == 'file' ? $this->manager->getFileManager()->getFileObject($setting->getValueByCulture($culture)) : $setting->getValueByCulture($culture);

                    if ($setting->getType() == 'file') {
                        $oneForm
                            ->add($culture, $widget, array_merge($options, $type['options']));
                    } else {
                        $oneForm
                            ->add($culture, $widget, $options);
                    }
                }
                $builder->add($oneForm);
            }
        }
        $builder->add('Beállítások mentése', 'submit', ['attr' => ['class' => 'btn btn-primary']]);
    }

    public function getName()
    {
        return 'settings';
    }

}