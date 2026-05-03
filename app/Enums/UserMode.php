<?php

declare(strict_types=1);

namespace App\Enums;

enum UserMode: string
{
    case Buyer = 'buyer';
    case Seller = 'seller';

    public function label(): string
    {
        return match ($this) {
            self::Buyer => 'Заказчик',
            self::Seller => 'Исполнитель',
        };
    }
}
