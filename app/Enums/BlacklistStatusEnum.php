<?php

namespace App\Enums;

enum BlacklistStatusEnum: string
{
    case NotYetChecked = 'not yet checked';
    case Valid = 'valid';
    case Invalid = 'invalid';

    public static function labels(): array
    {
        return [
            'not_yet_checked' => 'Not Yet Checked',
            'valid' => 'Valid',
            'invalid' => 'Invalid',
        ];
    }
}
