<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_searches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('search_type', 16)->default('tasks');
            $table->json('filters');
            $table->string('query')->nullable();
            $table->json('notify_via')->nullable();
            $table->string('frequency', 16)->default('instant');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_alert_at')->nullable();
            $table->timestamp('last_match_seen_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
