<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self DEDICATED()
 * @method static self RESELLER()
 * @method static self VPS()
 */
class ServerTypeEnum extends Enum
{
    const MAP_VALUE = [
        'DEDICATED' => 'dedicated',
        'RESELLER' => 'reseller',
        'VPS' => 'vps',
    ];
}
