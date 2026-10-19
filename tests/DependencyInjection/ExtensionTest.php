<?php

namespace Hgabka\SettingsBundle\Tests\DependencyInjection;

use Hgabka\SettingsBundle\DependencyInjection\Configuration;
use Hgabka\SettingsBundle\DependencyInjection\HgabkaSettingsExtension;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Hgabka\SettingsBundle\Model\BoolSettingType;
use Hgabka\SettingsBundle\Model\SettingTypeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ExtensionTest extends TestCase
{
    public function testRoleDefaults(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), []);

        $this->assertSame(['editor_role' => 'ROLE_SETTING_ADMIN', 'creator_role' => 'ROLE_SUPER_ADMIN'], $config);
    }

    public function testRolesBecomeParameters(): void
    {
        $container = new ContainerBuilder();
        (new HgabkaSettingsExtension())->load([['editor_role' => 'ROLE_E', 'creator_role' => 'ROLE_C']], $container);

        $this->assertSame('ROLE_E', $container->getParameter('hg_settings.editor_role'));
        $this->assertSame('ROLE_C', $container->getParameter('hg_settings.creator_role'));
    }

    public function testSettingTypesAreAutoconfigured(): void
    {
        $container = new ContainerBuilder();
        (new HgabkaSettingsExtension())->load([[]], $container);

        $conditionals = $container->getAutoconfiguredInstanceof();
        $this->assertArrayHasKey(SettingTypeInterface::class, $conditionals);
        $this->assertArrayHasKey('hg_setting.setting_type', $conditionals[SettingTypeInterface::class]->getTags());
    }

    public function testCompilerPassRegistersTaggedTypesInTheManager(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition(SettingsManager::class, new Definition(SettingsManager::class));
        $container->setDefinition('type.bool', (new Definition(BoolSettingType::class))->addTag('hg_setting.setting_type', ['alias' => 'b']));
        $container->setDefinition('type.other', (new Definition(BoolSettingType::class))->addTag('hg_setting.setting_type'));

        (new HgabkaSettingsExtension())->process($container);

        $calls = $container->getDefinition(SettingsManager::class)->getMethodCalls();
        $this->assertCount(2, $calls);
        $this->assertSame('addType', $calls[0][0]);
        $this->assertSame('b', $calls[0][1][1]);
        $this->assertSame('type.other', $calls[1][1][1]);
    }

    public function testCompilerPassDoesNothingWithoutManager(): void
    {
        $container = new ContainerBuilder();

        (new HgabkaSettingsExtension())->process($container);

        $this->assertFalse($container->has(SettingsManager::class));
    }
}
