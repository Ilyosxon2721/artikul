<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 16)->default('pending');

            // Offer details
            $table->longText('cover_letter')->nullable();
            $table->decimal('proposed_amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('UZS');
            $table->string('rate_type', 16)->default('fixed');
            $table->unsignedSmallInteger('proposed_deadline_days')->nullable();
            $table->json('portfolio_item_ids')->nullable();

            // Source
            $table->boolean('via_invitation')->default(false);
            $table->boolean('via_telegram')->default(false);

            // Lifecycle timestamps
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();

            $table->timestamps();

            $table->unique(['task_id', 'seller_id']);
            $table->index(['seller_id', 'status']);
            $table->index(['task_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
