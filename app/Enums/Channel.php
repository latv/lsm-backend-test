<?php

namespace App\Enums;

enum Channel: int
{
    case LTV1 = 1;
    case LTV2 = 2;
    case LTV3 = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
