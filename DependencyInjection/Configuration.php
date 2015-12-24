<?php

namespace HG\SettingsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hg_settings');

       $rootNode
            ->children()
                ->scalarNode('manager_class')->defaultValue('HG\SettingsBundle\Model\SettingsManager')->end()
                ->scalarNode('cache_class')->defaultValue('HG\SettingsBundle\Cache\SettingCache')->end()
                ->scalarNode('form_class')->defaultValue('HG\SettingsBundle\Form\SettingsType')->end()
                ->scalarNode('editor_role')->defaultValue('ROLE_SETTING_EDITOR')->end()
                ->scalarNode('creator_role')->defaultValue('ROLE_SETTING_CREATOR')->end()
                ->scalarNode('cache_dir')->defaultNull()->end()
                ->booleanNode('auto_delete_files')->defaultTrue()->end()
                ->arrayNode('types')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                          ->scalarNode('label')->end()
                          ->scalarNode('form_type')->defaultValue('text')->end()
                          ->arrayNode('options')
                          ->children()
                             ->scalarNode('choice_callback')->end()
                             ->booleanNode('expanded')->defaultFalse()->end()
                             ->scalarNode('empty_value')->defaultFalse()->end()
                             ->scalarNode('width')->defaultValue(500)->end()
                             ->arrayNode('choices')
                             ->prototype('scalar')->end()
                             ->end()
                             ->end()
                            ->end()
                          ->end()
                        ->end()
                    ->end()


                ->scalarNode('upload_dir')->defaultValue('uploads')->end()
            ->end()    ;
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
