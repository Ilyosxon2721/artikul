<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Reference data — always seeded.
        $this->call([
            MarketplaceSeeder::class,
            CategorySeeder::class,
            SpecializationSeeder::class,
        ]);

        // Demo data — local & testing environments only.
        if (app()->environment('local', 'testing')) {
            $this->call([
                DemoUserSeeder::class,
            ]);
        }
    }
}
