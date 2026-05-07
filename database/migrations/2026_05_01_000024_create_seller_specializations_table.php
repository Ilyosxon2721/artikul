<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_specializations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialization_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['seller_profile_id', 'specialization_id'], 'seller_spec_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_specializations');
    }
};
