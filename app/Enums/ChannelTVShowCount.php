<?php

namespace App\Enums;

enum ChannelTVShowCount: int
{
    case current = 1;
    case upcoming = 10;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
