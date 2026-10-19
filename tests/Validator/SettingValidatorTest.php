<?php

namespace Hgabka\SettingsBundle\Tests\Validator;

use Hgabka\SettingsBundle\Helper\SettingsManager;
use Hgabka\SettingsBundle\Validator\Setting;
use Hgabka\SettingsBundle\Validator\SettingValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class SettingValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        $manager = $this->createStub(SettingsManager::class);
        $manager->method('get')->willReturn(10);

        return new SettingValidator($manager);
    }

    private function constraint(string $comparator): Setting
    {
        $constraint = new Setting();
        $constraint->name = 'limit';
        $constraint->comparator = $comparator;

        return $constraint;
    }

    public function testEqualValuePasses(): void
    {
        $this->validator->validate(10, $this->constraint('equals'));

        $this->assertNoViolation();
    }

    public function testComparators(): void
    {
        $this->validator->validate(11, $this->constraint('>'));
        $this->validator->validate(9, $this->constraint('LESSER'));
        $this->assertNoViolation();

        $this->validator->validate(9, $this->constraint('>'));
        $this->buildViolation('The value {{ value }} does not satisfy the "{{ comparator }}" comparison with the setting.')->setParameter('{{ value }}', '9')->setParameter('{{ comparator }}', '>')->assertRaised();
    }

    public function testUnknownComparatorIsRejected(): void
    {
        $this->expectException(\Exception::class);
        $this->validator->validate(10, $this->constraint('~'));
    }

    public function testValidatedByTheSettingValidator(): void
    {
        $this->assertSame(SettingValidator::class, (new Setting())->validatedBy());
    }
}
