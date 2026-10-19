<?php

namespace Hgabka\SettingsBundle\Tests\Entity;

use Hgabka\SettingsBundle\Entity\Redirect;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Entity\SettingTranslation;
use PHPUnit\Framework\TestCase;

class SettingTest extends TestCase
{
    public function testTranslationEntityClass(): void
    {
        $this->assertSame(SettingTranslation::class, Setting::getTranslationEntityClass());
    }

    public function testValueByLocaleDependsOnCultureAwareness(): void
    {
        $setting = (new Setting())->setGeneralValue('general');
        $setting->translate('hu')->setValue('magyar');

        $this->assertFalse($setting->isCultureAware());
        $this->assertSame('general', $setting->getValueByLocale('hu'));

        $setting->setCultureAware(true);
        $this->assertSame('magyar', $setting->getValueByLocale('hu'));
        $this->assertSame('magyar', $setting->getValue('hu'));
        $this->assertNull($setting->getValueByLocale('en'));
    }

    public function testAccessorsAreFluent(): void
    {
        $setting = new Setting();

        $result = $setting->setName('site_name')->setType('string')->setEditable(false)->setVisible(false)->setRequired(true)->setId(4);

        $this->assertSame($setting, $result);
        $this->assertSame(['site_name', 'string', false, false, true, 4], [
            $setting->getName(), $setting->getType(), $setting->isEditable(), $setting->isVisible(), $setting->isRequired(), $setting->getId(),
        ]);
    }

    public function testRedirectAccessors(): void
    {
        $redirect = (new Redirect())->setDomain('example.com')->setOrigin('/old')->setTarget('/new')->setPermanent(true);

        $this->assertSame(['example.com', '/old', '/new', true], [
            $redirect->getDomain(), $redirect->getOrigin(), $redirect->getTarget(), $redirect->isPermanent(),
        ]);
    }
}
