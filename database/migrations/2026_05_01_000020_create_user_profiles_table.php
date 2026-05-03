<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name')->nullable();
            $table->text('short_bio')->nullable();
            $table->json('languages')->nullable();
            $table->string('phone_secondary', 32)->nullable();
            $table->string('telegram_username')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('instagram_username')->nullable();
            $table->string('website_url')->nullable();
            $table->json('notification_preferences')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
