<?php

namespace Hgabka\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\NotBlank;

class RedirectAdmin extends AbstractAdmin
{
    public function configureListFields(ListMapper $list): void
    {
        $list
            ->add('origin', null, [
                'label' => 'hg_settings.redirect.label.origin',
                'sortable' => false,
            ])
            ->add('target', null, [
                'label' => 'hg_settings.redirect.label.target',
                'sortable' => false,
            ])
            ->add('permanent', null, [
                'label' => 'hg_settings.redirect.label.permanent',
                'sortable' => false,
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    public function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('origin', null, [
                'label' => 'hg_settings.redirect.label.origin',
            ])
            ->add('target', null, [
                'label' => 'hg_settings.redirect.label.target',
            ])
            ->add('permanent', null, [
                'label' => 'hg_settings.redirect.label.permanent',
            ])
        ;
    }

    protected function configureBatchActions(array $actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('origin', null, [
                'label' => 'hg_settings.redirect.label.origin',
                'required' => true,
                'constraints' => new NotBlank(),
            ])
            ->add('target', null, [
                'label' => 'hg_settings.redirect.label.target',
                'required' => true,
                'constraints' => new NotBlank(),
            ])
            ->add('permanent', null, [
                'label' => 'hg_settings.redirect.label.permanent',
                'required' => false,
            ])
        ;
    }
}
