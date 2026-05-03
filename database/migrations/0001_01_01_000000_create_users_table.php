<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();

            // Identity
            $table->string('name');
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('phone', 32)->unique()->nullable();
            $table->string('password');
            $table->rememberToken();

            // Verification timestamps
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            // Profile basics (общие, для быстрого доступа без join)
            $table->string('avatar_url')->nullable();
            $table->string('locale', 5)->default('ru');
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('timezone', 64)->default('Asia/Tashkent');
            $table->string('current_mode', 16)->default('buyer');

            // Referral
            $table->string('referral_code', 32)->unique()->nullable();
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();

            // Telegram bot link
            $table->string('telegram_chat_id')->nullable()->index();

            // Anti-fraud
            $table->string('passport_number_hash', 64)->nullable()->index();

            // Admin/system
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_super_admin')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->string('banned_reason')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->string('last_ip', 45)->nullable();
            $table->timestamp('last_active_at')->nullable()->index();
            $table->timestamp('password_changed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_admin', 'is_banned']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
