<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->date('work_date');
            $table->unsignedInteger('minutes');
            $table->string('description');
            $table->boolean('is_disputed')->default(false);
            $table->text('dispute_reason')->nullable();
            $table->timestamp('disputed_at')->nullable();
            $table->boolean('is_billed')->default(false);
            $table->timestamps();

            $table->index(['contract_id', 'work_date']);
            $table->index('seller_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
