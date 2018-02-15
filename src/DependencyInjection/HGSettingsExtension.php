<?php

namespace HG\SettingsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HGSettingsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('hg_settings.cache_class', $config['cache_class']);
        $container->setParameter('hg_settings.manager_class', $config['manager_class']);
        $container->setParameter('hg_settings.form_class', $config['form_class']);
        $container->setParameter('hg_settings.editor_role', $config['editor_role']);
        $container->setParameter('hg_settings.creator_role', $config['creator_role']);
        $container->setParameter('hg_settings.cache_dir', !empty($config['cache_dir']) ? $config['cache_dir'] : $container->getParameter('kernel.cache_dir'));
        $container->setParameter('hg_settings.upload_dir', $config['upload_dir']);
        $container->setParameter('hg_settings.auto_delete_files', $config['auto_delete_files']);

        $defaultTypes = array(
        'input' => array(
          'label' => 'hg_settings_type_input',
          'form_type' => 'text',
          'options' => array()
           ),
        'textarea' => array(
          'label' => 'hg_settings_type_textarea',
          'form_type' => 'textarea',
          'options' => array()
          ),
        'checkbox' => array(
          'label' => 'hg_settings_type_checkbox',
          'form_type' => 'checkbox',
          'options' => array()
          ),
        'yesno' => array(
          'label' => 'hg_settings_type_yesno',
          'form_type' => 'choice',
          'options' => array(
            'choices' => array('1' => 'hg_settings_choice_yes', 0 => 'hg_settings_choice_no'),
            'expanded' => true,
            'empty_value' => false
          )
          ),
        'select' => array(
          'label' => 'hg_settings_type_select',
          'form_type' => 'choice',
          'options' => array(
            'choice_callback' => 'getOptionsArrayForSetting',
          )
          ),
        'file' => array(
          'label' => 'hg_settings_type_file',
          'form_type' => 'file_repository',
          'options' => array('repository_type' => 'setting')
          ),
        'wysiwyg' => array(
          'label' => 'hg_settings_type_wysiwyg',
          'form_type' => 'ckeditor',
          'options' => array(
            'width' => 500
          )
          ),

        );

        $container->setParameter('hg_settings.types', array_merge($defaultTypes, $config['types']));
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
