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
        Schema::create('materials', function (Blueprint $table) {
            $table->id('material_id');
            $table->foreignId('uploader_id')
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade'); // Hapus materi jika user dihapus
            $table->foreignId('related_request_id')
                   ->nullable()
                   ->constrained('requests', 'request_id')
                   ->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('tags')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
