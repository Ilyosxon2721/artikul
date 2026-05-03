<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    /**
     * 30+ marketplace-industry specializations.
     * Each entry: [slug, name_ru, name_uz, name_en, parent_category_slug].
     */
    private const ITEMS = [
        // Management
        ['wb-manager', 'Менеджер Wildberries', 'Wildberries menejeri', 'Wildberries manager', 'management'],
        ['ozon-manager', 'Менеджер Ozon', 'Ozon menejeri', 'Ozon manager', 'management'],
        ['uzum-manager', 'Менеджер Uzum Market', 'Uzum Market menejeri', 'Uzum Market manager', 'management'],
        ['ym-manager', 'Менеджер Yandex Market', 'Yandex Market menejeri', 'Yandex Market manager', 'management'],
        ['multi-marketplace-manager', 'Мульти-маркетплейс-менеджер', 'Mulʼti marketpleys menejeri', 'Multi-marketplace manager', 'management'],
        ['operations-manager', 'Операционный менеджер', 'Operatsion menejer', 'Operations manager', 'management'],
        ['supply-chain-specialist', 'Специалист по поставкам', 'Yetkazib berish boʻyicha mutaxassis', 'Supply chain specialist', 'management'],

        // Content
        ['infographic-designer', 'Дизайнер инфографики', 'Infografika dizayneri', 'Infographic designer', 'content'],
        ['rich-content-designer', 'Дизайнер Rich-контента', 'Rich-kontent dizayneri', 'Rich content designer', 'content'],
        ['marketplace-copywriter', 'Копирайтер маркетплейсов', 'Marketpleys kopirayteri', 'Marketplace copywriter', 'content'],
        ['marketplace-seo-specialist', 'SEO-специалист маркетплейсов', 'Marketpleys SEO mutaxassisi', 'Marketplace SEO specialist', 'content'],
        ['ui-ux-designer', 'UI/UX дизайнер', 'UI/UX dizayner', 'UI/UX designer', 'content'],
        ['translator-listings', 'Переводчик карточек', 'Kartochkalar tarjimoni', 'Listings translator', 'content'],

        // Ads
        ['internal-ads-specialist', 'Специалист по внутренней рекламе', 'Ichki reklama mutaxassisi', 'Internal ads specialist', 'ads'],
        ['external-traffic-specialist', 'Специалист по внешнему трафику', 'Tashqi trafik mutaxassisi', 'External traffic specialist', 'ads'],
        ['influencer-manager', 'Менеджер по работе с блогерами', 'Bloggerlar bilan ishlash menejeri', 'Influencer manager', 'ads'],
        ['social-media-manager', 'SMM-менеджер', 'SMM menejer', 'Social media manager', 'ads'],

        // Analytics
        ['unit-economics-analyst', 'Аналитик юнит-экономики', 'Yunit iqtisodiyoti tahlilchisi', 'Unit economics analyst', 'analytics'],
        ['niche-analyst', 'Аналитик ниш', 'Nisha tahlilchisi', 'Niche analyst', 'analytics'],
        ['data-analyst', 'Дата-аналитик', 'Maʼlumotlar tahlilchisi', 'Data analyst', 'analytics'],
        ['marketplace-consultant', 'Консультант по маркетплейсам', 'Marketpleys boʻyicha konsultant', 'Marketplace consultant', 'analytics'],

        // Photo/Video
        ['product-photographer', 'Предметный фотограф', 'Mahsulot fotografi', 'Product photographer', 'photo-video'],
        ['lifestyle-photographer', 'Lifestyle-фотограф', 'Lifestyle fotograf', 'Lifestyle photographer', 'photo-video'],
        ['model-photographer', 'Модельный фотограф', 'Model fotografi', 'Model photographer', 'photo-video'],
        ['videographer', 'Видеограф', 'Videograf', 'Videographer', 'photo-video'],
        ['video-editor', 'Видео-монтажёр', 'Video montajchi', 'Video editor', 'photo-video'],
        ['3d-artist', '3D-художник', '3D rassom', '3D artist', 'photo-video'],

        // Legal & accounting
        ['marketplace-accountant', 'Бухгалтер маркетплейсов', 'Marketpleys buxgalteri', 'Marketplace accountant', 'legal-accounting'],
        ['ved-specialist', 'Специалист ВЭД', 'TIF mutaxassisi', 'Foreign trade specialist', 'legal-accounting'],
        ['marketplace-lawyer', 'Юрист маркетплейсов', 'Marketpleys yuristi', 'Marketplace lawyer', 'legal-accounting'],
        ['trademark-specialist', 'Специалист по товарным знакам', 'Tovar belgisi boʻyicha mutaxassis', 'Trademark specialist', 'legal-accounting'],
        ['tax-consultant', 'Налоговый консультант', 'Soliq konsultanti', 'Tax consultant', 'legal-accounting'],
    ];

    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        foreach (self::ITEMS as $index => [$slug, $nameRu, $nameUz, $nameEn, $categorySlug]) {
            Specialization::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categories[$categorySlug] ?? null,
                    'name_ru' => $nameRu,
                    'name_uz' => $nameUz,
                    'name_en' => $nameEn,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
