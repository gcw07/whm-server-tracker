<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self dedicated()
 * @method static self reseller()
 * @method static self vps()
 */
class ServerTypeEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'dedicated' => 'Dedicated',
            'reseller' => 'Reseller',
            'vps' => 'VPS',
        ];
    }
}
