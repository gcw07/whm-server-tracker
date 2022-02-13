<?php

namespace App\Enums;

enum ServerTypeEnum: string
{
    case Dedicated = 'dedicated';
    case Reseller = 'reseller';
    case Vps = 'vps';
}
