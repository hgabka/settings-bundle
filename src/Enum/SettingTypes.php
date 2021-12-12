<?php

namespace Hgabka\SettingsBundle\Enum;

use Hgabka\UtilsBundle\Enums\ConstantsChoiceLoader;

class SettingTypes extends ConstantsChoiceLoader
{
    public const STR = 'str';
    public const INT = 'int';
    public const BOOL = 'bool';
    public const EMAIL = 'email';
    public const FLOAT = 'float';

    public static function getI18nPrefix()
    {
        return 'hg_settings.types.';
    }
}
