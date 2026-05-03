<?php

declare(strict_types=1);

namespace App\Enums;

enum SellerLevel: string
{
    case Junior = 'junior';
    case Middle = 'middle';
    case Senior = 'senior';

    public function label(): string
    {
        return match ($this) {
            self::Junior => 'Junior',
            self::Middle => 'Middle',
            self::Senior => 'Senior',
        };
    }
}
