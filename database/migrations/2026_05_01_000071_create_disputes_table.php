<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opened_by')->constrained('users')->cascadeOnDelete();
            $table->string('opened_by_role', 16);

            $table->string('status', 24)->default('open');
            $table->string('reason_code', 64);
            $table->longText('description');
            $table->json('evidence')->nullable();

            // Admin resolution
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_summary')->nullable();
            $table->unsignedTinyInteger('buyer_share_percent')->nullable();
            $table->unsignedTinyInteger('seller_share_percent')->nullable();

            // SLA tracking
            $table->timestamp('sla_due_at')->nullable();

            $table->timestamps();

            $table->index(['contract_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
