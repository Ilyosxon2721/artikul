<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 24)->default('pending');

            // Documents (encrypted S3 paths)
            $table->string('id_document_path')->nullable();
            $table->string('id_selfie_path')->nullable();
            $table->json('marketplace_screenshots')->nullable();
            $table->string('recommendation_path')->nullable();

            // Anti-fraud
            $table->string('passport_number_hash', 64)->nullable()->unique();

            // Admin
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_comment')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
