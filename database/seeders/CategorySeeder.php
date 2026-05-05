<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Tree of root categories with their subcategories.
     * Per ТЗ section 3.3: ведение, контент, реклама, аналитика, фото-видео, юр-бух.
     */
    private const TREE = [
        [
            'slug' => 'management',
            'name_ru' => 'Ведение магазина',
            'name_uz' => 'Doʻkonni boshqarish',
            'name_en' => 'Store management',
            'icon' => 'store',
            'children' => [
                ['slug' => 'management-full', 'name_ru' => 'Полное ведение', 'name_uz' => 'Toʻliq boshqaruv', 'name_en' => 'Full management'],
                ['slug' => 'management-listings', 'name_ru' => 'Создание карточек', 'name_uz' => 'Kartochkalar yaratish', 'name_en' => 'Listings creation'],
                ['slug' => 'management-warehousing', 'name_ru' => 'Поставки и склад', 'name_uz' => 'Yetkazib berish va ombor', 'name_en' => 'Supply & warehousing'],
                ['slug' => 'management-pricing', 'name_ru' => 'Ценообразование', 'name_uz' => 'Narx belgilash', 'name_en' => 'Pricing'],
            ],
        ],
        [
            'slug' => 'content',
            'name_ru' => 'Контент',
            'name_uz' => 'Kontent',
            'name_en' => 'Content',
            'icon' => 'image',
            'children' => [
                ['slug' => 'content-infographic', 'name_ru' => 'Инфографика', 'name_uz' => 'Infografika', 'name_en' => 'Infographics'],
                ['slug' => 'content-rich', 'name_ru' => 'Rich-контент / А+', 'name_uz' => 'Rich-kontent', 'name_en' => 'Rich content / A+'],
                ['slug' => 'content-copywriting', 'name_ru' => 'Описания и тексты', 'name_uz' => 'Tavsiflar va matnlar', 'name_en' => 'Descriptions & copy'],
                ['slug' => 'content-seo', 'name_ru' => 'SEO-оптимизация карточек', 'name_uz' => 'Kartochkalar SEO', 'name_en' => 'Listing SEO'],
            ],
        ],
        [
            'slug' => 'ads',
            'name_ru' => 'Реклама и продвижение',
            'name_uz' => 'Reklama va targʻibot',
            'name_en' => 'Ads & promotion',
            'icon' => 'megaphone',
            'children' => [
                ['slug' => 'ads-internal', 'name_ru' => 'Внутренняя реклама', 'name_uz' => 'Ichki reklama', 'name_en' => 'Internal ads'],
                ['slug' => 'ads-external', 'name_ru' => 'Внешний трафик', 'name_uz' => 'Tashqi trafik', 'name_en' => 'External traffic'],
                ['slug' => 'ads-bloggers', 'name_ru' => 'Блогеры и инфлюенсеры', 'name_uz' => 'Bloggerlar', 'name_en' => 'Influencers'],
            ],
        ],
        [
            'slug' => 'analytics',
            'name_ru' => 'Аналитика',
            'name_uz' => 'Analitika',
            'name_en' => 'Analytics',
            'icon' => 'chart-bar',
            'children' => [
                ['slug' => 'analytics-unit', 'name_ru' => 'Юнит-экономика', 'name_uz' => 'Yunit iqtisodiyot', 'name_en' => 'Unit economics'],
                ['slug' => 'analytics-niche', 'name_ru' => 'Аналитика ниши', 'name_uz' => 'Nishani tahlili', 'name_en' => 'Niche analysis'],
                ['slug' => 'analytics-competitors', 'name_ru' => 'Анализ конкурентов', 'name_uz' => 'Raqobatchilar tahlili', 'name_en' => 'Competitor research'],
            ],
        ],
        [
            'slug' => 'photo-video',
            'name_ru' => 'Фото и видео',
            'name_uz' => 'Foto va video',
            'name_en' => 'Photo & video',
            'icon' => 'camera',
            'children' => [
                ['slug' => 'photo-product', 'name_ru' => 'Предметная фотосъёмка', 'name_uz' => 'Mahsulot suratga olish', 'name_en' => 'Product photography'],
                ['slug' => 'photo-model', 'name_ru' => 'Модельная съёмка', 'name_uz' => 'Modelli suratga olish', 'name_en' => 'Model photography'],
                ['slug' => 'video-product', 'name_ru' => 'Видеообзоры товаров', 'name_uz' => 'Mahsulot video sharhi', 'name_en' => 'Product videos'],
                ['slug' => 'video-3d', 'name_ru' => '3D и анимация', 'name_uz' => '3D va animatsiya', 'name_en' => '3D & animation'],
            ],
        ],
        [
            'slug' => 'legal-accounting',
            'name_ru' => 'Юридические и бухгалтерия',
            'name_uz' => 'Yuridik va buxgalteriya',
            'name_en' => 'Legal & accounting',
            'icon' => 'scale',
            'children' => [
                ['slug' => 'accounting-marketplace', 'name_ru' => 'Бухгалтерия маркетплейсов', 'name_uz' => 'Marketpleys buxgalteriyasi', 'name_en' => 'Marketplace accounting'],
                ['slug' => 'legal-ved', 'name_ru' => 'ВЭД и таможня', 'name_uz' => 'TIF va bojxona', 'name_en' => 'Foreign trade & customs'],
                ['slug' => 'legal-trademark', 'name_ru' => 'Регистрация товарных знаков', 'name_uz' => 'Tovar belgisi roʻyxati', 'name_en' => 'Trademark registration'],
                ['slug' => 'legal-disputes', 'name_ru' => 'Споры с маркетплейсом', 'name_uz' => 'Marketpleys bilan nizolar', 'name_en' => 'Marketplace disputes'],
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::TREE as $rootIndex => $root) {
            $rootCategory = Category::updateOrCreate(
                ['slug' => $root['slug']],
                [
                    'parent_id' => null,
                    'name_ru' => $root['name_ru'],
                    'name_uz' => $root['name_uz'],
                    'name_en' => $root['name_en'],
                    'icon' => $root['icon'],
                    'is_active' => true,
                    'sort_order' => $rootIndex + 1,
                ],
            );

            foreach ($root['children'] as $childIndex => $child) {
                Category::updateOrCreate(
                    ['slug' => $child['slug']],
                    [
                        'parent_id' => $rootCategory->id,
                        'name_ru' => $child['name_ru'],
                        'name_uz' => $child['name_uz'],
                        'name_en' => $child['name_en'],
                        'is_active' => true,
                        'sort_order' => $childIndex + 1,
                    ],
                );
            }
        }
    }
}
