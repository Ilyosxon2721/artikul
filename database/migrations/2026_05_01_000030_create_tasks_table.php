<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();

            // Type & status
            $table->string('type', 16);
            $table->string('status', 24)->default('draft');

            // Identity
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('description')->nullable();

            // Marketplace & category
            $table->json('marketplace_ids')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained()->nullOnDelete();

            // Volume
            $table->string('volume_unit', 32)->nullable();
            $table->unsignedInteger('volume_amount')->nullable();

            // Budget
            $table->string('budget_type', 16)->default('fixed');
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->string('currency', 3)->default('UZS');

            // Timeline
            $table->timestamp('deadline_at')->nullable();
            $table->unsignedSmallInteger('estimated_duration_days')->nullable();

            // Requirements
            $table->string('required_seller_level', 16)->nullable();
            $table->boolean('require_verified_seller')->default(false);
            $table->json('required_languages')->nullable();
            $table->string('required_country', 2)->nullable();
            $table->json('requirements')->nullable();

            // Publication
            $table->boolean('invitation_only')->default(false);
            $table->json('invited_seller_ids')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // Counters (cached aggregates)
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('proposals_count')->default(0);
            $table->unsignedInteger('invitations_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['buyer_id', 'status']);
            $table->index(['status', 'published_at']);
            $table->index('deadline_at');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
