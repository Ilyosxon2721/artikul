<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Currency;
use App\Enums\SellerLevel;
use App\Enums\UserMode;
use App\Models\BuyerProfile;
use App\Models\Marketplace;
use App\Models\SellerProfile;
use App\Models\Specialization;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoUserSeeder extends Seeder
{
    /**
     * Local-only demo data: 1 admin, 5 buyers, 10 sellers (per ТЗ promp 2 step 8).
     * Skipped automatically in production by DatabaseSeeder.
     */
    public function run(): void
    {
        $this->createAdmin();

        for ($i = 1; $i <= 5; $i++) {
            $this->createBuyer($i);
        }

        for ($i = 1; $i <= 10; $i++) {
            $this->createSeller($i);
        }
    }

    private function createAdmin(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@artikul.uz'],
            [
                'name' => 'Artikul Admin',
                'username' => 'admin',
                'phone' => '+998900000001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'locale' => 'ru',
                'country_code' => 'UZ',
                'city' => 'Коканд',
                'timezone' => 'Asia/Tashkent',
                'current_mode' => UserMode::Buyer,
                'is_admin' => true,
                'is_super_admin' => true,
                'referral_code' => 'admin'.Str::random(4),
            ],
        );

        UserProfile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'display_name' => 'Artikul Team',
                'short_bio' => 'Команда платформы Artikul',
                'languages' => ['ru', 'uz', 'en'],
            ],
        );
    }

    private function createBuyer(int $i): void
    {
        $email = "buyer{$i}@artikul.test";
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => "Тестовый заказчик {$i}",
                'username' => "buyer{$i}",
                'phone' => '+99890000010'.$i,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'locale' => 'ru',
                'country_code' => 'UZ',
                'city' => 'Ташкент',
                'timezone' => 'Asia/Tashkent',
                'current_mode' => UserMode::Buyer,
                'referral_code' => "buyer{$i}".Str::random(4),
            ],
        );

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'display_name' => "Demo Buyer {$i}",
                'short_bio' => 'Demo-аккаунт заказчика для тестов',
                'languages' => ['ru'],
            ],
        );

        BuyerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => "Demo Brand {$i}",
                'description' => 'Тестовый бренд для разработки и QA',
                'marketplace_codes' => ['wb', 'ozon', 'uzum'],
                'skus_count' => $i * 50,
                'revenue_range' => '1m-5m',
                'default_currency' => Currency::UZS,
            ],
        );
    }

    private function createSeller(int $i): void
    {
        $email = "seller{$i}@artikul.test";
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => "Тестовый исполнитель {$i}",
                'username' => "seller{$i}",
                'phone' => '+99890000020'.$i,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'locale' => 'ru',
                'country_code' => 'UZ',
                'city' => $i % 2 === 0 ? 'Коканд' : 'Ташкент',
                'timezone' => 'Asia/Tashkent',
                'current_mode' => UserMode::Seller,
                'referral_code' => "seller{$i}".Str::random(4),
            ],
        );

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'display_name' => "Demo Seller {$i}",
                'short_bio' => 'Demo-аккаунт исполнителя для тестов',
                'languages' => ['ru', 'uz'],
            ],
        );

        $sellerProfile = SellerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'headline' => 'Менеджер маркетплейсов · '.$i.' лет опыта',
                'bio' => 'Помогаю продавцам развивать продажи на Wildberries, Ozon и Uzum Market. Создаю карточки, настраиваю рекламу, веду аналитику.',
                'languages' => ['ru', 'uz'],
                'hourly_rate' => 80_000 + ($i * 10_000),
                'currency' => Currency::UZS,
                'min_order_amount' => 500_000,
                'available_hours_per_week' => 30 + ($i * 2),
                'open_to_long_term' => true,
                'is_verified' => $i <= 3,
                'verified_at' => $i <= 3 ? now() : null,
                'rating_avg' => 4.5 + (($i % 5) * 0.1),
                'reviews_count' => $i * 3,
                'completion_rate' => 95.5,
                'on_time_delivery_rate' => 96.0,
                'is_searchable' => true,
            ],
        );

        $marketplaces = Marketplace::whereIn('code', ['wb', 'ozon', 'uzum'])->get();
        foreach ($marketplaces as $mp) {
            $sellerProfile->marketplaces()->syncWithoutDetaching([
                $mp->id => [
                    'level' => SellerLevel::Middle->value,
                    'experience_months' => $i * 6,
                ],
            ]);
        }

        $specializations = Specialization::whereIn('slug', [
            'wb-manager', 'ozon-manager', 'uzum-manager', 'infographic-designer', 'product-photographer',
        ])
            ->inRandomOrder()
            ->take(3)
            ->get();

        foreach ($specializations as $index => $spec) {
            $sellerProfile->specializations()->syncWithoutDetaching([
                $spec->id => ['is_primary' => $index === 0],
            ]);
        }
    }
}
