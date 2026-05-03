<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Guest = 'guest';
    case Buyer = 'buyer';
    case Seller = 'seller';
    case VerifiedSeller = 'verified_seller';
    case ProSeller = 'pro_seller';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Guest => 'Гость',
            self::Buyer => 'Заказчик',
            self::Seller => 'Исполнитель',
            self::VerifiedSeller => 'Верифицированный исполнитель',
            self::ProSeller => 'Pro Seller',
            self::Admin => 'Администратор',
            self::SuperAdmin => 'Супер-администратор',
        };
    }
}
