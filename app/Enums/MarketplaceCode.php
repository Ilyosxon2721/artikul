<?php

declare(strict_types=1);

namespace App\Enums;

enum MarketplaceCode: string
{
    case Wildberries = 'wb';
    case Ozon = 'ozon';
    case Uzum = 'uzum';
    case YandexMarket = 'ym';
    case SberMegaMarket = 'sber';
    case KazanExpress = 'ke';

    public function label(): string
    {
        return match ($this) {
            self::Wildberries => 'Wildberries',
            self::Ozon => 'Ozon',
            self::Uzum => 'Uzum Market',
            self::YandexMarket => 'Yandex Market',
            self::SberMegaMarket => 'СберМегаМаркет',
            self::KazanExpress => 'KazanExpress',
        };
    }

    public function countryCode(): string
    {
        return match ($this) {
            self::Wildberries, self::Ozon, self::YandexMarket, self::SberMegaMarket => 'RU',
            self::Uzum, self::KazanExpress => 'UZ',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Wildberries => '#CB11AB',
            self::Ozon => '#005BFF',
            self::Uzum => '#7C3AED',
            self::YandexMarket => '#FFCC00',
            self::SberMegaMarket => '#21A038',
            self::KazanExpress => '#FF5C00',
        };
    }
}
