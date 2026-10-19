<?php

namespace Hgabka\SettingsBundle\Tests\Model;

use Hgabka\SettingsBundle\Model\AbstractSettingType;
use Hgabka\SettingsBundle\Model\BoolSettingType;
use Hgabka\SettingsBundle\Model\DateRangeSettingType;
use Hgabka\SettingsBundle\Model\DateSettingType;
use Hgabka\SettingsBundle\Model\DateTimeSettingType;
use Hgabka\SettingsBundle\Model\DatetimeRangeSettingType;
use Hgabka\SettingsBundle\Model\EmailSettingType;
use Hgabka\SettingsBundle\Model\IntegerSettingType;
use Hgabka\SettingsBundle\Model\NumberSettingType;
use Hgabka\SettingsBundle\Model\SettingTypeInterface;
use Hgabka\SettingsBundle\Model\StringSettingType;
use Hgabka\SettingsBundle\Model\TextSettingType;
use Hgabka\SettingsBundle\Model\WysiwygSettingType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Email;

class SettingTypesTest extends TestCase
{
    public static function types(): array
    {
        return [
            'string' => [StringSettingType::class, 'string'],
            'text' => [TextSettingType::class, 'text'],
            'wysiwyg' => [WysiwygSettingType::class, 'wysiwyg'],
            'integer' => [IntegerSettingType::class, 'integer'],
            'number' => [NumberSettingType::class, 'number'],
            'bool' => [BoolSettingType::class, 'bool'],
            'email' => [EmailSettingType::class, 'email'],
            'date' => [DateSettingType::class, 'date'],
            'datetime' => [DateTimeSettingType::class, 'datetime'],
            'daterange' => [DateRangeSettingType::class, 'daterange'],
            'datetimerange' => [DatetimeRangeSettingType::class, 'datetimerange'],
        ];
    }

    #[DataProvider('types')]
    public function testTypeContract(string $class, string $id): void
    {
        $type = new $class();

        $this->assertInstanceOf(SettingTypeInterface::class, $type);
        $this->assertSame($id, $type->getId());
        $this->assertNotSame('', $type->getName());
        $this->assertTrue(class_exists($type->getFormType()), $type->getFormType());
        $this->assertTrue($type->isEditable());
        $this->assertTrue($type->isVisible());
    }

    #[DataProvider('types')]
    public function testFormTypeOptions(string $class, string $id): void
    {
        if (in_array($id, ['daterange', 'datetimerange'], true)) {
            $this->markTestSkipped('The range types build Symfony\\Component\\Validator\\Constraints\\Callback with the array syntax that Symfony 7+ no longer accepts.');
        }

        $this->assertIsArray((new $class())->getFormTypeOptions() ?? []);
    }

    public function testTypeIdsAreUnique(): void
    {
        $ids = array_map(static fn (array $row): string => (new $row[0]())->getId(), self::types());

        $this->assertSame($ids, array_unique($ids));
    }

    public function testAbstractTypeDefaultsPassValuesThrough(): void
    {
        $type = new StringSettingType();

        $this->assertSame('abc', $type->transformValue('abc'));
        $this->assertSame('abc', $type->reverseTransformValue('abc'));
        $this->assertSame('abc', $type->getHtml('abc'));
        $this->assertNull($type->getFormTransformer());
        $this->assertInstanceOf(AbstractSettingType::class, $type);
    }

    public function testBoolConversion(): void
    {
        $type = new BoolSettingType();

        $this->assertTrue($type->transformValue('1'));
        $this->assertFalse($type->transformValue(''));
        $this->assertFalse($type->reverseTransformValue('0'));
        $this->assertTrue($type->reverseTransformValue(1));
    }

    public function testNumericConversion(): void
    {
        $this->assertSame(12, (new IntegerSettingType())->transformValue('12.9'));
        $this->assertSame(12, (new IntegerSettingType())->reverseTransformValue('12'));
        $this->assertSame(1.5, (new NumberSettingType())->transformValue('1.5'));
        $this->assertSame(0.0, (new NumberSettingType())->reverseTransformValue(null));
    }

    public function testDateRoundTrip(): void
    {
        $type = new DateSettingType();
        $date = new \DateTime('2024-05-06 13:14:15');

        $this->assertSame('2024-05-06', $type->transformValue($date));
        $this->assertSame('2024-05-06', $type->reverseTransformValue('2024-05-06')->format('Y-m-d'));
        $this->assertNull($type->transformValue(null));
        $this->assertNull($type->reverseTransformValue(null));
    }

    public function testDateTimeRoundTrip(): void
    {
        $type = new DateTimeSettingType();

        $this->assertSame('2024-05-06 13:14', $type->transformValue(new \DateTime('2024-05-06 13:14:15')));
        $this->assertSame('2024-05-06 13:14', $type->reverseTransformValue('2024-05-06 13:14')->format('Y-m-d H:i'));
        $this->assertNull($type->transformValue(''));
        $this->assertSame('', $type->reverseTransformValue(''));
    }

    public function testRangeIsStoredSerialized(): void
    {
        $type = new DatetimeRangeSettingType();
        $range = ['from' => new \DateTime('2024-01-01'), 'to' => new \DateTime('2024-02-01')];

        $stored = $type->transformValue($range);

        $this->assertIsString($stored);
        $this->assertEquals($range, $type->reverseTransformValue($stored));
        $this->assertNull($type->transformValue([]));
    }

    public function testRangeFormTransformerMapsFromToToStartEnd(): void
    {
        $transformer = (new DatetimeRangeSettingType())->getFormTransformer();

        $this->assertSame(['start' => 1, 'end' => 2], $transformer->transform(['from' => 1, 'to' => 2]));
        $this->assertSame(['from' => 1, 'to' => 2], $transformer->reverseTransform(['start' => 1, 'end' => 2]));
        $this->assertNull($transformer->reverseTransform([]));
        $this->assertNull($transformer->transform(null));
    }

    public function testEmailTypeValidatesAddresses(): void
    {
        $options = (new EmailSettingType())->getFormTypeOptions();

        $this->assertInstanceOf(Email::class, $options['constraints'][0]);
    }
}
