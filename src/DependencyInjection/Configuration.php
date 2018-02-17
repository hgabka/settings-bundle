<?php

namespace Hgabka\SettingsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hgabka_settings');
        $rootNode
            ->children()
            ->scalarNode('editor_role')->cannotBeEmpty()->defaultValue('ROLE_SETTING_ADMIN')->end()
            ->scalarNode('creator_role')->cannotBeEmpty()->defaultValue('ROLE_SUPER_ADMIN')->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
