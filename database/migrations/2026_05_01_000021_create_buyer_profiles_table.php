<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name')->nullable();
            $table->text('description')->nullable();
            $table->json('marketplace_codes')->nullable();
            $table->unsignedInteger('skus_count')->nullable();
            $table->string('revenue_range', 32)->nullable();
            $table->string('default_currency', 3)->default('UZS');

            // Aggregated buyer stats
            $table->unsignedInteger('total_tasks_published')->default(0);
            $table->unsignedInteger('total_contracts_completed')->default(0);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->string('total_spent_currency', 3)->default('UZS');
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->decimal('on_time_payment_rate', 5, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('rating_avg');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_profiles');
    }
};
