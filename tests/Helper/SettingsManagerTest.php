<?php

namespace Hgabka\SettingsBundle\Tests\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Hgabka\SettingsBundle\Model\BoolSettingType;
use Hgabka\SettingsBundle\Model\IntegerSettingType;
use Hgabka\SettingsBundle\Model\StringSettingType;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingsManagerTest extends TestCase
{
    private string $cacheDir;

    /** @var list<Setting> */
    private array $settings = [];

    protected function setUp(): void
    {
        $this->cacheDir = sys_get_temp_dir() . '/hg_settings_' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        if (!is_dir($this->cacheDir)) {
            return;
        }
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->cacheDir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($this->cacheDir);
    }

    private function setting(string $name, string $type, ?string $general, array $translated = []): Setting
    {
        $setting = (new Setting())->setName($name)->setType($type)->setGeneralValue($general)->setCultureAware([] !== $translated);
        foreach ($translated as $locale => $value) {
            $setting->translate($locale)->setValue($value);
        }

        return $this->settings[] = $setting;
    }

    private function manager(): SettingsManager
    {
        $repository = $this->createStub(EntityRepository::class);
        $repository->method('findAll')->willReturnCallback(fn (): array => $this->settings);
        $doctrine = $this->createStub(Registry::class);
        $doctrine->method('getRepository')->willReturn($repository);

        $utils = $this->createStub(HgabkaUtils::class);
        $utils->method('getAvailableLocales')->willReturn(['hu', 'en']);
        $utils->method('getCurrentLocale')->willReturnCallback(static fn (?string $locale = null): string => $locale ?? 'hu');

        $manager = new SettingsManager($doctrine, $utils, $this->cacheDir);
        $manager->addType(new StringSettingType(), 'string');
        $manager->addType(new IntegerSettingType(), 'integer');
        $manager->addType(new BoolSettingType(), 'bool');

        return $manager;
    }

    public function testGetReadsTypedGeneralValue(): void
    {
        $this->setting('site_name', 'string', 'Acme');
        $this->setting('per_page', 'integer', '25');
        $this->setting('maintenance', 'bool', '1');
        $manager = $this->manager();

        $this->assertSame('Acme', $manager->get('site_name'));
        $this->assertSame(25, $manager->get('per_page'));
        $this->assertTrue($manager->get('maintenance'));
    }

    public function testCultureAwareSettingReturnsValuePerLocale(): void
    {
        $this->setting('slogan', 'string', null, ['hu' => 'Szlogen', 'en' => 'Slogan']);
        $manager = $this->manager();

        $this->assertSame('Szlogen', $manager->get('slogan'));
        $this->assertSame('Slogan', $manager->get('slogan', 'en'));
    }

    public function testUnknownSettingReturnsDefault(): void
    {
        $manager = $this->manager();

        $this->assertNull($manager->get('missing'));
        $this->assertSame('fallback', $manager->get('missing', null, 'fallback'));
    }

    public function testMagicCallRejectsOtherVerbs(): void
    {
        $this->expectException(\Exception::class);
        $this->manager()->setSiteName('x');
    }

    public function testCacheIsRegeneratedFromTheDatabaseOnFirstAccess(): void
    {
        $this->setting('site_name', 'string', 'Acme');
        $manager = $this->manager();

        $data = $manager->getCacheData();

        $this->assertSame(['value' => ['hu' => 'Acme', 'en' => 'Acme'], 'type' => 'string'], $data['site_name']);
    }

    public function testAddAndRemoveSettingToCache(): void
    {
        $manager = $this->manager();
        $manager->getCacheData();
        $setting = $this->setting('new_one', 'string', 'value');

        $this->assertTrue($manager->addSettingToCache($setting));
        $this->assertArrayHasKey('new_one', $manager->getCacheData());

        $this->assertTrue($manager->removeFromCache('new_one'));
        $this->assertArrayNotHasKey('new_one', $manager->getCacheData());
    }

    public function testClearCacheForcesRegeneration(): void
    {
        $this->setting('a', 'string', '1');
        $manager = $this->manager();
        $manager->getCacheData();
        $this->setting('b', 'string', '2');

        $manager->clearCache();

        $this->assertSame(['a', 'b'], array_keys($manager->getCacheData()));
    }

    public function testGetValuesReturnsAllTypedValues(): void
    {
        $this->setting('site_name', 'string', 'Acme');
        $this->setting('per_page', 'integer', '25');
        $this->setting('slogan', 'string', null, ['hu' => 'Szlogen', 'en' => 'Slogan']);
        $manager = $this->manager();

        $this->assertSame(['site_name' => 'Acme', 'per_page' => 25, 'slogan' => 'Szlogen'], $manager->getValues());
        $this->assertSame('Slogan', $manager->getValues('en')['slogan']);
    }

    public function testReplaceSettingsSubstitutesScalarValues(): void
    {
        $this->setting('site_name', 'string', 'Acme');
        $this->setting('per_page', 'integer', '25');
        $this->setting('maintenance', 'bool', '1');
        $manager = $this->manager();

        $result = $manager->replaceSettings('[site_name] shows [per_page] items, maintenance=[maintenance]', '[', ']');

        $this->assertSame('Acme shows 25 items, maintenance=[maintenance]', $result);
    }

    public function testReplaceSettingsCallbackCanTransformValues(): void
    {
        $this->setting('site_name', 'string', 'Acme');

        $result = $this->manager()->replaceSettings('{site_name}', '{', '}', static fn ($value, string $name): string => strtoupper($value));

        $this->assertSame('ACME', $result);
    }

    public function testTypesAreSortedByPriorityAndResolvedById(): void
    {
        $manager = $this->manager();

        $this->assertInstanceOf(IntegerSettingType::class, $manager->getType('integer'));
        $this->assertSame(['string', 'integer', 'bool'], array_map(static fn ($type): string => $type->getId(), array_values($manager->getTypes())));
        $this->assertSame(['string', 'integer', 'bool'], array_values($manager->getTypeChoices()));
    }

    public function testUnknownTypeIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->manager()->getType('nope');
    }

    public function testEmptyTypeIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->manager()->getType('');
    }

    public function testSetValuesByCulturesTransformsPerLocale(): void
    {
        $setting = (new Setting())->setType('integer')->setCultureAware(true);

        $this->manager()->setValuesByCultures($setting, ['hu' => '12', 'en' => '']);

        $this->assertSame('12', $setting->translate('hu')->getValue());
        $this->assertNull($setting->translate('en')->getValue());
    }

    public function testAddConstraintsAcceptsSingleConstraintOrList(): void
    {
        $manager = $this->manager();
        $options = [];

        $manager->addConstraints($options, new NotBlank());
        $manager->addConstraints($options, [new Email()]);

        $this->assertCount(2, $options['constraints']);
    }

    public function testAddConstraintsWithoutUniquenessKeepsExistingOnes(): void
    {
        $manager = $this->manager();
        $options = ['constraints' => [new NotBlank()]];

        $manager->addConstraints($options, [new NotBlank()], false);

        $this->assertCount(2, $options['constraints']);
    }

    public function testEmptyConstraintsLeaveOptionsUntouched(): void
    {
        $manager = $this->manager();
        $options = ['a' => 1];

        $manager->addConstraints($options, []);

        $this->assertSame(['a' => 1], $options);
    }

    public function testLocalesComeFromUtils(): void
    {
        $this->assertSame(['hu', 'en'], $this->manager()->getLocales());
    }
}
