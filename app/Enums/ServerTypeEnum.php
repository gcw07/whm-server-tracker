<?php

namespace App\Enums;

enum ServerTypeEnum: string
{
    case Dedicated = 'dedicated';
    case Reseller = 'reseller';
    case Vps = 'vps';

    public static function labels(): array
    {
        return [
            'dedicated' => 'Dedicated',
            'reseller' => 'Reseller',
            'vps' => 'VPS',
        ];
    }
}
