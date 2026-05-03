<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('cover_url')->nullable();
            $table->json('media')->nullable();
            $table->json('marketplace_codes')->nullable();
            $table->foreignId('specialization_id')->nullable()->constrained()->nullOnDelete();
            $table->json('result_metrics')->nullable();
            $table->string('client_name')->nullable();
            $table->date('completed_on')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['seller_profile_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
