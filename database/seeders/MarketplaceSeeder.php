<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Marketplace;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $marketplaces = [
            [
                'code' => 'wb',
                'name_ru' => 'Wildberries',
                'name_uz' => 'Wildberries',
                'name_en' => 'Wildberries',
                'country_code' => 'RU',
                'color' => '#CB11AB',
                'website_url' => 'https://wildberries.ru',
                'sort_order' => 1,
            ],
            [
                'code' => 'ozon',
                'name_ru' => 'Ozon',
                'name_uz' => 'Ozon',
                'name_en' => 'Ozon',
                'country_code' => 'RU',
                'color' => '#005BFF',
                'website_url' => 'https://ozon.ru',
                'sort_order' => 2,
            ],
            [
                'code' => 'uzum',
                'name_ru' => 'Uzum Market',
                'name_uz' => 'Uzum Market',
                'name_en' => 'Uzum Market',
                'country_code' => 'UZ',
                'color' => '#7C3AED',
                'website_url' => 'https://uzum.uz',
                'sort_order' => 3,
            ],
            [
                'code' => 'ym',
                'name_ru' => 'Yandex Market',
                'name_uz' => 'Yandex Market',
                'name_en' => 'Yandex Market',
                'country_code' => 'RU',
                'color' => '#FFCC00',
                'website_url' => 'https://market.yandex.ru',
                'sort_order' => 4,
            ],
            [
                'code' => 'sber',
                'name_ru' => 'СберМегаМаркет',
                'name_uz' => 'SberMegaMarket',
                'name_en' => 'SberMegaMarket',
                'country_code' => 'RU',
                'color' => '#21A038',
                'website_url' => 'https://megamarket.ru',
                'sort_order' => 5,
            ],
            [
                'code' => 'ke',
                'name_ru' => 'KazanExpress',
                'name_uz' => 'KazanExpress',
                'name_en' => 'KazanExpress',
                'country_code' => 'UZ',
                'color' => '#FF5C00',
                'website_url' => 'https://kazanexpress.ru',
                'sort_order' => 6,
            ],
        ];

        foreach ($marketplaces as $marketplace) {
            Marketplace::updateOrCreate(
                ['code' => $marketplace['code']],
                $marketplace + ['is_active' => true],
            );
        }
    }
}
