<?php

namespace Hgabka\SettingsBundle\Admin;

use Hgabka\SettingsBundle\Enum\SettingTypes;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\Choice;
use Sonata\CoreBundle\Validator\ErrorElement;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;

class SettingAdmin extends AbstractAdmin
{
    /** @var SettingsManager */
    private $manager;

    public function setManager($manager)
    {
        $this->manager = $manager;
    }


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'hg_settings_label_name'])
            ->add('culture_aware', 'checkbox', ['required' => false, 'label' => 'hg_settings_label_culture_aware'])
            ->add('options', 'textarea', ['required' => false, 'label' => 'hg_settings_label_options'])
            ->add('type', 'choice', [
                'label'       => 'hg_settings_label_type',
                'choices'     => $this->manager->getTypeChoices(),
                'constraints' => [
                    new Choice([
                        'choices' => (new SettingTypes())->loadSonataChoices(),
                        'message' => 'setting.type.invalid',
                    ]),
                ],
            ])
            ->add('translations', TranslationsType::class, [
                'label'    => false,
                'locales'  => ['hu'],
                'required' => false,
                'fields'   => [
                    'description' => [
                        'label'      => 'hg_settings_label_description',
                        'required'   => false,
                        'field_type' => 'textarea',
                    ],
                    'value'       => [
                        'field_type' => 'hidden',
                        'required'   => false,
                    ],

                ],
            ])
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->end();
    }

    public function getBatchActions()
    {
        return [];
    }


    public function postUpdate($object)
    {
        $this->manager->clearCache();
    }

    public function postPersist($object)
    {
        $this->manager->clearCache();
    }

    public function postRemove($object)
    {
        $this->manager->clearCache();
    }

}