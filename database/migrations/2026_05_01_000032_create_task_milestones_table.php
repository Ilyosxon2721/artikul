<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_milestones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable();
            $table->unsignedSmallInteger('order_index')->default(0);
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('acceptance_criteria')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('UZS');
            $table->timestamp('deadline_at')->nullable();
            $table->string('status', 24)->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'order_index']);
            $table->index('contract_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_milestones');
    }
};
