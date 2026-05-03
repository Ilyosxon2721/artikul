<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_marketplaces', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketplace_id')->constrained()->cascadeOnDelete();
            $table->string('level', 16)->default('middle');
            $table->unsignedInteger('experience_months')->nullable();
            $table->timestamps();

            $table->unique(['seller_profile_id', 'marketplace_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_marketplaces');
    }
};
