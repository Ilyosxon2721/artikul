<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Public info
            $table->string('headline', 80)->nullable();
            $table->longText('bio')->nullable();
            $table->string('video_intro_url')->nullable();
            $table->json('languages')->nullable();

            // Pricing
            $table->decimal('hourly_rate', 15, 2)->nullable();
            $table->string('currency', 3)->default('UZS');
            $table->decimal('min_order_amount', 15, 2)->nullable();

            // Availability
            $table->unsignedSmallInteger('available_hours_per_week')->nullable();
            $table->boolean('open_to_long_term')->default(true);

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            // Aggregated stats
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->string('earnings_currency', 3)->default('UZS');
            $table->unsignedInteger('total_contracts_completed')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->unsignedInteger('response_time_minutes')->nullable();
            $table->decimal('on_time_delivery_rate', 5, 2)->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);

            // Search/visibility
            $table->boolean('is_pro')->default(false);
            $table->boolean('is_searchable')->default(true);
            $table->unsignedInteger('profile_views')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_verified', 'rating_avg']);
            $table->index(['is_searchable', 'is_pro']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
