<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('faculty')->nullable()->after('attachment_file');
            $table->string('course_name')->nullable()->after('faculty');
            $table->string('semester')->nullable()->after('course_name');
            $table->string('academic_year')->nullable()->after('semester');
            $table->enum('category', [
                'Catatan', 'Tugas', 'Jawaban UTS/UAS', 'Kuis', 
                'Presentasi', 'Mindmap', 'Diskusi', 'Latihan', 
                'Proyek', 'Lain-Lain'
            ])->default('Lain-Lain')->after('academic_year');
            $table->integer('upvotes_count')->default(0)->after('category');
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['faculty', 'course_name', 'semester', 'academic_year', 'category', 'upvotes_count']);
        });
    }
};
