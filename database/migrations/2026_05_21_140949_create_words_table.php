<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('english_word');
            $table->string('bangla_meaning');
            $table->string('status')->default('unseen');
            $table->boolean('is_favorite')->default(false);
            $table->boolean('read_later')->default(false);
            $table->unsignedInteger('srs_interval')->default(0);
            $table->unsignedInteger('srs_repetitions')->default(0);
            $table->decimal('srs_ease_factor', 4, 2)->default(2.50);
            $table->dateTime('next_review_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'english_word']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};
