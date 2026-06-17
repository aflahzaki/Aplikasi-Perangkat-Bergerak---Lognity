<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID'); // Pakai locale Indonesia

        // 1. Matikan Foreign Key Checks untuk reset data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::table('requests')->truncate();
        DB::table('materials')->truncate();
        DB::table('interactions')->truncate();
        DB::table('point_logs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🧹 Database berhasil dibersihkan. Mulai seeding...');

        // ==========================================
        // 1. SEEDING USERS (2 Admin + 20 User)
        // ==========================================
        
        // A. Create Admins
        $admins = [
            [
                'username' => 'superadmin',
                'email' => 'root@forum.com',
                'role' => 'Superadmin',
                'current_level' => 'Artefak',
                'profil' => 'admin_bot.png'
            ],
            [
                'username' => 'admin_prodi',
                'email' => 'admin@forum.com',
                'role' => 'Admin',
                'current_level' => 'Aktif',
                'profil' => 'admin_human.png'
            ]
        ];

        foreach ($admins as $adm) {
            DB::table('users')->insert([
                'username' => $adm['username'],
                'email' => $adm['email'],
                'password' => Hash::make('password123'),
                'role' => $adm['role'],
                'current_level' => $adm['current_level'],
                'points' => 9999,
                'profil' => $adm['profil'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // B. Create 20 Users Natural
        $levels = ['Maba', 'Aktif', 'Aktif', 'Calon', 'Artefak']; // Bobot lebih banyak di Aktif
        $userIds = [];

        for ($i = 0; $i < 20; $i++) {
            $id = DB::table('users')->insertGetId([
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->freeEmail,
                'password' => Hash::make('password123'),
                'role' => 'User',
                'current_level' => $levels[array_rand($levels)],
                'points' => rand(0, 500),
                'profil' => 'ppdefault.png',
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ]);
            $userIds[] = $id;
        }

        $this->command->info('✅ 22 User berhasil dibuat (2 Admin + 20 User).');

        // ==========================================
        // 2. DATASET TOPIK KULIAH (Biar Natural)
        // ==========================================
        
        $courses = [
            ['prodi' => 'Teknik Informatika', 'matkul' => 'Algoritma Pemrograman', 'smt' => 1],
            ['prodi' => 'Teknik Informatika', 'matkul' => 'Basis Data', 'smt' => 3],
            ['prodi' => 'Teknik Informatika', 'matkul' => 'Kecerdasan Buatan', 'smt' => 5],
            ['prodi' => 'Hukum', 'matkul' => 'Pengantar Ilmu Hukum', 'smt' => 1],
            ['prodi' => 'Hukum', 'matkul' => 'Hukum Pidana', 'smt' => 3],
            ['prodi' => 'Ekonomi', 'matkul' => 'Akuntansi Dasar', 'smt' => 1],
            ['prodi' => 'Ekonomi', 'matkul' => 'Manajemen Pemasaran', 'smt' => 4],
            ['prodi' => 'DKV', 'matkul' => 'Nirmana', 'smt' => 1],
            ['prodi' => 'Umum', 'matkul' => 'KKN / Magang', 'smt' => 7],
        ];

        $titles = [
            "Minta materi {matkul} dong",
            "Ada yang punya jawaban UAS {matkul} tahun lalu?",
            "Bingung tugas {matkul} bagian ini",
            "Catatan {matkul} dosen A lengkap",
            "Resume materi {matkul} pertemuan 5-7",
            "Contoh proposal buat {matkul}",
            "Spill kisi-kisi UTS {matkul}",
        ];

        $descriptions = [
            "Tolong dong kating atau temen2 yang punya, besok deadline nih.",
            "Gue gak masuk pas pertemuan ini, ada yang catet ga?",
            "Susah banget materinya, bagi tips belajarnya dong.",
            "File di e-learning corrupt, ada yang sempet download?",
            "Buat referensi belajar aja, makasih sebelumnya!",
            "Kalau ada yang punya pdf bukunya boleh drop link ya.",
        ];

        // ==========================================
        // 3. SEEDING REQUESTS (50 Item)
        // ==========================================

        $requestIds = [];

        for ($i = 0; $i < 50; $i++) {
            $user = $userIds[array_rand($userIds)];
            $course = $courses[array_rand($courses)];
            
            // Generate Title Natural
            $titleTemplate = $titles[array_rand($titles)];
            $descTemplate = $descriptions[array_rand($descriptions)];
            $finalDesc = str_replace('{matkul}', $course['matkul'], $titleTemplate) . ". " . $descTemplate;

            $requestId = DB::table('requests')->insertGetId([
                'user_id' => $user,
                'description' => $finalDesc,
                'faculty' => $course['prodi'],
                'course_name' => $course['matkul'],
                'semester' => $course['smt'],
                'academic_year' => '2024/2025',
                'category' => $faker->randomElement(['Catatan', 'Tugas', 'Jawaban UTS/UAS', 'Diskusi', 'Lain-Lain']),
                'status' => $faker->randomElement(['Open', 'Open', 'Open', 'Resolved']), // Lebih banyak Open
                'upvotes_count' => rand(0, 20),
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                'updated_at' => now(),
            ]);

            $requestIds[] = $requestId;

            // ==========================================
            // 4. SEEDING INTERACTIONS & MATERIALS
            // ==========================================
            
            // Randomly add Interactions (Comments/Answers) per request
            $numInteractions = rand(0, 4); 
            
            for ($j = 0; $j < $numInteractions; $j++) {
                $responderId = $userIds[array_rand($userIds)];
                // Pastikan responder bukan si pembuat request (opsional, tapi lebih natural)
                while($responderId == $user) {
                    $responderId = $userIds[array_rand($userIds)];
                }

                $type = $faker->randomElement(['Comment', 'Answer']);
                
                $comments = [
                    "Coba cek di drive angkatan bang.",
                    "Wah sama, gue juga nyari ini.",
                    "Udah gue kirim ya, cek email.",
                    "Up gan, butuh juga.",
                    "Dosen siapa? Kalau Pak Budi biasanya ambil dari buku X.",
                    "Sabar ya, emang susah matkul itu.",
                    "Ini gue ada dikit catetan, semoga membantu."
                ];

                DB::table('interactions')->insert([
                    'user_id' => $responderId,
                    'request_id' => $requestId,
                    'type' => $type,
                    'content' => $comments[array_rand($comments)],
                    'is_accepted_answer' => ($type == 'Answer' && rand(0,10) > 8),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Randomly add Material (jika request ini ada yang jawab pakai file)
            if (rand(0, 10) > 7) { // 30% chance ada material
                $uploaderId = $userIds[array_rand($userIds)];
                DB::table('materials')->insert([
                    'uploader_id' => $uploaderId,
                    'related_request_id' => $requestId,
                    'title' => "File: " . $course['matkul'],
                    'description' => "Ini file yang diminta tadi.",
                    'file_path' => 'uploads/dummy/' . $faker->word . '.pdf',
                    'tags' => $course['matkul'] . ',2024',
                    'download_count' => rand(1, 50),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ 50 Requests + Interaksi & Material berhasil dibuat.');
        $this->command->info('🚀 Seeding selesai! Akun Admin: superadmin / password123');
    }
}