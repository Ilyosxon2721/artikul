<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 16);

            // Ratings (1-5)
            $table->unsignedTinyInteger('rating_overall');
            $table->unsignedTinyInteger('rating_quality')->nullable();
            $table->unsignedTinyInteger('rating_communication')->nullable();
            $table->unsignedTinyInteger('rating_deadline')->nullable();
            $table->unsignedTinyInteger('rating_professionalism')->nullable();

            // Buyer-only fields (when role=seller, this is buyer rating buyer)
            $table->unsignedTinyInteger('rating_clarity')->nullable();
            $table->unsignedTinyInteger('rating_payment_timeliness')->nullable();

            $table->text('comment');
            $table->string('would_work_again', 16)->nullable();

            // Blind-method
            $table->boolean('is_visible')->default(false);
            $table->timestamp('published_at')->nullable();

            // Author response (1 reply allowed)
            $table->text('reply_text')->nullable();
            $table->timestamp('replied_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['contract_id', 'author_id']);
            $table->index(['target_id', 'role', 'is_visible']);
            $table->index('rating_overall');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
