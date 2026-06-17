<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id('interaction_id');
            $table->foreignId('user_id')
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade');
            $table->foreignId('material_id')
                  ->nullable()
                  ->constrained('materials', 'material_id')
                  ->onDelete('cascade');
            $table->foreignId('request_id')
                  ->nullable()
                  ->constrained('requests', 'request_id')
                  ->onDelete('cascade');
            $table->enum('type', ['Upvote', 'Comment', 'Answer']);
            $table->text('content')->nullable();
            $table->boolean('is_accepted_answer')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
