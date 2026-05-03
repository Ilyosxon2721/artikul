<?php

declare(strict_types=1);

namespace App\Enums;

enum Currency: string
{
    case UZS = 'UZS';
    case RUB = 'RUB';
    case KZT = 'KZT';
    case USD = 'USD';

    public function symbol(): string
    {
        return match ($this) {
            self::UZS => 'сум',
            self::RUB => '₽',
            self::KZT => '₸',
            self::USD => '$',
        };
    }
}
