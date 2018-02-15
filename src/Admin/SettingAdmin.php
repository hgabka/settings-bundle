<?php

namespace HG\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\Choice;

class SettingAdmin extends Admin
{
  private $manager;
  private $securityContext;

  public function setManager($manager)
  {
    $this->manager = $manager;
  }

  public function setSecurityContext($context)
  {
    $this->securityContext = $context;
  }


  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
      ->add('name', null, array('label' => 'hg_settings_label_name'))
      ->add('culture_aware', 'checkbox', array('required' => false, 'label' => 'hg_settings_label_culture_aware'))
      ->add('options', 'textarea', array('required' => false, 'label' => 'hg_settings_label_options'))
      ->add('type', 'choice', array(
        'label' => 'hg_settings_label_type',
        'choices' => $this->manager->getTypeChoices(),
        'constraints' => array(
          new Choice(array(
           'choices' => array_keys($this->manager->getTypeChoices()),
           'message' => 'setting.type.invalid'
           ))
        )
        ))
      ->add('translations', 'a2lix_translations', array(
       'label' => false,
       'locales' => $this->manager->getLocales(),
       'required' => false,
       'fields' => array(
         'description' => array(
           'label' => 'hg_settings_label_description',
           'required' => false,
           'field_type' => 'textarea',
           ),
          'value' => array(
            'field_type' => 'hidden',
            'required' => false
          )

         )
      ))
    ;
  }



  public function validate(ErrorElement $errorElement, $object)
  {
    $errorElement
      ->end()
    ;
  }

  public function getBatchActions()
  {
    return array();
  }

  public function isGranted($name, $object = null)
  {
    $container = $this->getConfigurationPool()->getContainer();
    if (in_array($name, array('CREATE', 'EDIT', 'DELETE')))
    {
      return $this->securityContext->isGranted($container->getParameter('hg_settings.creator_role'));
    }

    if (!$this->securityContext->isGranted($container->getParameter('hg_settings.editor_role')))
    {
      return false;
    }

    return parent::isGranted($name, $object);
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