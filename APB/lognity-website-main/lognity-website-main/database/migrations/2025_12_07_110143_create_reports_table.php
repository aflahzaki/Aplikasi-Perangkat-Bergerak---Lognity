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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->foreignId('reporter_id')
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade');
            $table->text('reason');
            $table->enum('status', ['Pending', 'Reviewed', 'Resolved', 'Dismissed'])
                  ->default('Pending');
            // 1. Target Materi
            $table->foreignId('target_material_id')
                  ->nullable()
                  ->constrained('materials', 'material_id')
                  ->onDelete('cascade');
            // 2. Target User (Jika melaporkan akun seseorang)
            $table->foreignId('target_user_id')
                  ->nullable()
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade');
            // 3. Target Request
            $table->foreignId('target_request_id')
                  ->nullable()
                  ->constrained('requests', 'request_id')
                  ->onDelete('cascade');
            // 4. Target Interaction (Komentar/Jawaban)
            $table->foreignId('target_interaction_id')
                  ->nullable()
                  ->constrained('interactions', 'interaction_id')
                  ->onDelete('cascade');

            // created_at & updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
