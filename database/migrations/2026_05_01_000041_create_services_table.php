<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_profile_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained()->nullOnDelete();
            $table->json('marketplace_codes')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('currency', 3)->default('UZS');
            $table->unsignedSmallInteger('delivery_days')->default(7);
            $table->unsignedSmallInteger('revisions_included')->default(2);
            $table->json('deliverables')->nullable();
            $table->string('cover_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('orders_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['seller_profile_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
