<?php

namespace Hgabka\SettingsBundle\DependencyInjection;

use Hgabka\SettingsBundle\Helper\SettingsManager;
use Hgabka\SettingsBundle\Model\SettingTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HgabkaSettingsExtension extends Extension implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $voterDefinition = $container->getDefinition('hg_settings.setting_voter');
        $voterDefinition->replaceArgument(1, $config['editor_role']);
        $voterDefinition->replaceArgument(2, $config['creator_role']);

        $container->setParameter('hg_settings.editor_role', $config['editor_role']);
        $container->setParameter('hg_settings.creator_role', $config['creator_role']);

        $container
            ->registerForAutoconfiguration(SettingTypeInterface::class)
            ->addTag('hg_setting.setting_type')
        ;
    }

    public function process(ContainerBuilder $container): void
    {
        // always first check if the primary service is defined
        if (!$container->has(SettingsManager::class)) {
            return;
        }

        $definition = $container->findDefinition(SettingsManager::class);

        // find all service IDs with the app.mail_transport tag
        $taggedServices = $container->findTaggedServiceIds('hg_setting.setting_type');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $type = new Reference($id);
                $definition->addMethodCall('addType', [
                    $type,
                    $attributes['alias'] ?? $id,
                ]);
            }
        }
    }
}
