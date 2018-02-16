<?php

namespace Hgabka\SettingsBundle\Enum;

use Hgabka\UtilsBundle\Enums\ConstantsChoiceLoader;

class RefreshIntervalChoices extends ConstantsChoiceLoader
{
    const STR = 'str';
    const INT = 'int';
    const BOOL = 'bool';
    const EMAIL = 'email';
    const FLOAT = 'float';

    public static function getI18nPrefix()
    {
        return 'hg_settings.types.';
    }
}