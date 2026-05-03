<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proposal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();

            $table->string('status', 24)->default('in_progress');
            $table->string('contract_type', 16)->default('gig');

            // Terms
            $table->decimal('agreed_amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('UZS');
            $table->decimal('hourly_rate', 15, 2)->nullable();
            $table->timestamp('agreed_deadline')->nullable();
            $table->string('payment_method', 32)->nullable();

            // Lifecycle timestamps
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Counters
            $table->boolean('has_dispute')->default(false);
            $table->unsignedSmallInteger('revisions_used')->default(0);
            $table->unsignedSmallInteger('revisions_limit')->default(3);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
