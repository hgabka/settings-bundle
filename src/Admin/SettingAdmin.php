<?php

namespace Hgabka\SettingsBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SettingAdmin extends AbstractAdmin
{
    /** @var SettingsManager */
    private $manager;

    public function setManager($manager)
    {
        $this->manager = $manager;
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

    public function getModelName()
    {
        return 'hg_settings.admin.model_name';
    }

    /**
     * Get the list of actions that can be accessed directly from the dashboard.
     *
     * @return array
     */
    public function getDashboardActions()
    {
        $actions = [];
        $container = $this->getConfigurationPool()->getContainer();
        $authChecker = $container->get('security.authorization_checker');

        if ($authChecker->isGranted($container->getParameter('hg_settings.creator_role'))) {
            $actions['create'] = [
                'label' => 'link_add',
                'translation_domain' => 'SonataAdminBundle',
                'template' => $this->getTemplate('action_create'),
                'url' => $this->generateUrl('create'),
                'icon' => 'plus-circle',
            ];
        }

        if ($authChecker->isGranted($container->getParameter('hg_settings.editor_role'))) {
            $actions['list'] = [
                'label' => 'link_list',
                'translation_domain' => 'SonataAdminBundle',
                'url' => $this->generateUrl('list'),
                'icon' => 'list',
            ];
        }

        return $actions;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'hg_settings.label.name'])
            ->add('type', ChoiceType::class, [
                'label' => 'hg_settings.label.type',
                'choices' => $this->manager->getTypeChoices(),
            ])
            ->add('culture_aware', CheckboxType::class, ['required' => false, 'label' => 'hg_settings.label.culture_aware'])
            ->add('required', CheckboxType::class, [
                'required' => false,
                'label' => 'hg_settings.label.required',
            ])
            ->add('editable', CheckboxType::class, [
                'required' => false,
                'label' => 'hg_settings.label.editable',
            ])
            ->add('visible', CheckboxType::class, [
                'required' => false,
                'label' => 'hg_settings.label.visible',
            ])
            ->add('translations', TranslationsType::class, [
                'label' => false,
                'locales' => $this->manager->getLocales(),
                'required' => false,
                'fields' => [
                    'description' => [
                        'label' => 'hg_settings.label.description',
                        'required' => false,
                        'field_type' => TextareaType::class,
                    ],
                    'value' => [
                        'field_type' => HiddenType::class,
                        'required' => false,
                    ],
                ],
            ])
        ;
    }
}
