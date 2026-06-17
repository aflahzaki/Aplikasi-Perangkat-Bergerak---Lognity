<?php

// d:\APB\Lognity\config\lognity.php

$badges = [];

// 1. XP Badges (25 Badges)
$xpTargets = [0, 50, 100, 250, 500, 1000, 1500, 2000, 2500, 3000, 4000, 5000, 6000, 7500, 10000, 15000, 20000, 25000, 30000, 40000, 50000, 60000, 75000, 90000, 100000];
$xpNames = ['Si Anak Baru', 'Pemanasan', 'Pencari Ilmu', 'Mulai Aktif', 'Rakyat Biasa', 'Pengejar Mimpi', 'Orang Sibuk', 'Kutu Buku', 'Pejuang Nilai', 'Si Paling Ambis', 'Otak Kanan', 'Penakluk Kelas', 'Mahasiswa Teladan', 'Bintang Kelas', 'Sultan XP', 'Ahli Nujum', 'Sang Jenius', 'Dewan Mahasiswa', 'Asisten Dosen', 'Dosen Muda', 'Profesor Muda', 'Suhu Besar', 'Legenda Kampus', 'Dewa Akademik', 'Entitas Kosmik'];
$xpIcons = ['👶', '🚶', '🔍', '🏃', '👤', '💭', '💼', '📚', '⚔️', '🔥', '🧠', '👑', '🏅', '⭐', '💰', '🔮', '💡', '🏛️', '👨‍🏫', '🎓', '🔬', '🧘', '🗿', '🌌', '👽'];
$colors = ['bg-blue-100 text-blue-800', 'bg-red-100 text-red-800', 'bg-green-100 text-green-800', 'bg-yellow-100 text-yellow-800', 'bg-purple-100 text-purple-800', 'bg-pink-100 text-pink-800', 'bg-indigo-100 text-indigo-800', 'bg-teal-100 text-teal-800', 'bg-orange-100 text-orange-800', 'bg-emerald-100 text-emerald-800'];

for ($i = 0; $i < 25; $i++) {
    $colorStr = $colors[array_rand($colors)];
    $badges[] = ['id' => 'xp_'.$i, 'metric' => 'xp', 'target' => $xpTargets[$i], 'name' => $xpNames[$i], 'icon' => $xpIcons[$i], 'desc' => 'Mengumpulkan '.$xpTargets[$i].' XP', 'color' => $colorStr, 'raw_color' => explode('-', explode(' ', $colorStr)[0])[1]];
}

// 2. Request/Asker Badges (25 Badges)
$reqTargets = [1, 2, 3, 5, 10, 15, 20, 25, 30, 40, 50, 75, 100, 150, 200, 250, 300, 400, 500, 600, 700, 800, 900, 1000, 9999];
$reqNames = ['Tanya Pertama', 'Penanya Pemula', 'Kepincut Nanya', 'Si Kepo', 'Wartawan Kampus', 'Banyak Nanya', 'Si Paling Nanya', 'Jurnalis Investigasi', 'Tukang Interview', 'Penggali Info', 'Detektif Rahasia', 'Mata-Mata', 'Ahli Bertanya', 'Sang Narasumber', 'Duta Pertanyaan', 'Mesin Pencari', 'Penanya Kritis', 'Filsuf Kampus', 'Socrates', 'Plato', 'Aristoteles', 'Sang Penanya Agung', 'Maha Penanya', 'Dewa Pertanyaan', 'Raja Spoiler'];
$reqIcons = ['🙋', '❓', '🤔', '👀', '🎤', '🗣️', '🧐', '🕵️', '📹', '⛏️', '🔎', '🥷', '🗣️', '🎙️', '📰', '🤖', '🤯', '📜', '🏛️', '🏛️', '🏛️', '👑', '🌠', '🌌', '🃏'];
for ($i = 0; $i < 25; $i++) {
    $colorStr = $colors[array_rand($colors)];
    $badges[] = ['id' => 'req_'.$i, 'metric' => 'request', 'target' => $reqTargets[$i], 'name' => $reqNames[$i], 'icon' => $reqIcons[$i], 'desc' => 'Membuat '.$reqTargets[$i].' Post/Pertanyaan', 'color' => $colorStr, 'raw_color' => explode('-', explode(' ', $colorStr)[0])[1]];
}

// 3. Answer Badges (25 Badges)
$ansTargets = [1, 2, 5, 10, 15, 20, 25, 30, 40, 50, 75, 100, 150, 200, 250, 300, 400, 500, 600, 700, 800, 900, 1000, 1500, 2000];
$ansNames = ['Penjawab Pertama', 'Bantu Teman', 'Orang Baik', 'Suka Menolong', 'Pahlawan Bertopeng', 'Asisten Sukarela', 'Teman Setia', 'Kakak Tingkat', 'Si Paling Tahu', 'Dewa Penolong', 'Malaikat Kampus', 'Wikipedia Berjalan', 'Kalkulator Hidup', 'Google Manusia', 'Perpustakaan Berjalan', 'Chatbot Lognity', 'Ahli Segala Hal', 'Sang Pencerah', 'Bintang Harapan', 'Orang Suci', 'Dewa Jawaban', 'Penjaga Kebijaksanaan', 'Suhu Agung', 'Titik Terang', 'Entitas Penjawab'];
$ansIcons = ['💬', '👍', '🤝', '🙌', '🦸', '💪', '🐕', '👱', '🧠', '👼', '🕊️', '🌐', '🧮', '💻', '📚', '🤖', '🧩', '🌅', '🌟', '😇', '🔱', '🛡️', '⛩️', '🕯️', '🌀'];
for ($i = 0; $i < 25; $i++) {
    $colorStr = $colors[array_rand($colors)];
    $badges[] = ['id' => 'ans_'.$i, 'metric' => 'answer', 'target' => $ansTargets[$i], 'name' => $ansNames[$i], 'icon' => $ansIcons[$i], 'desc' => 'Memberikan '.$ansTargets[$i].' Jawaban', 'color' => $colorStr, 'raw_color' => explode('-', explode(' ', $colorStr)[0])[1]];
}

// 4. Accepted Answer Badges (25 Badges)
$accTargets = [1, 2, 3, 5, 10, 15, 20, 25, 30, 40, 50, 60, 75, 100, 125, 150, 200, 250, 300, 350, 400, 450, 500, 750, 1000];
$accNames = ['Satu Solusi', 'Solusi Tepat', 'Dapat Dipercaya', 'Ahli Fixer', 'Problem Solver', 'Konsultan Andalan', 'Tukang Servis', 'Pemecah Masalah', 'Pawang Bug', 'Ahli Strategi', 'Master Solusi', 'Dokter Kampus', 'Penyelamat IPK', 'Pahlawan Lulus', 'Sang Eksekutor', 'Dewa Akurasi', 'Tembakan Jitu', 'Mata Elang', 'Sang Validasi', 'Hakim Kebenaran', 'Raja Benar', 'Sang Mutlak', 'Dewa Solusi', 'Penentu Takdir', 'Alpha & Omega'];
$accIcons = ['✅', '🎯', '🤝', '🔧', '🧩', '💼', '🧰', '🔨', '🐛', '♟️', '🥇', '🩺', '🎓', '🦸‍♂️', '⚔️', '🏹', '🔫', '🦅', '⚖️', '👨‍⚖️', '👑', '💯', '✨', '⏳', '🌌'];
for ($i = 0; $i < 25; $i++) {
    $colorStr = $colors[array_rand($colors)];
    $badges[] = ['id' => 'acc_'.$i, 'metric' => 'accepted', 'target' => $accTargets[$i], 'name' => $accNames[$i], 'icon' => $accIcons[$i], 'desc' => 'Mendapatkan '.$accTargets[$i].' Jawaban Diterima (Best Answer)', 'color' => $colorStr, 'raw_color' => explode('-', explode(' ', $colorStr)[0])[1]];
}

// 5. Upload Material Badges (25 Badges)
$upTargets = [1, 2, 3, 5, 10, 15, 20, 25, 30, 40, 50, 60, 75, 100, 125, 150, 200, 250, 300, 350, 400, 450, 500, 750, 1000];
$upNames = ['Bagi Catatan', 'Uploader Pemula', 'Tukang Share', 'Dermawan', 'Bandar Materi', 'Agen Distribusi', 'Penyedia Jasa', 'Penyumbang Ilmu', 'Donatur Buku', 'Arsiparis', 'Pustakawan', 'Penjaga Perpus', 'Menteri Pendidikan', 'Sumber Ilmu', 'Bapak Literasi', 'Pahlawan Buku', 'Bank Soal', 'Kolektor Tugas', 'Museum Kampus', 'Sang Kurator', 'Raja Data', 'Pusat Informasi', 'Server Berjalan', 'Database Hidup', 'Dewa Arsip'];
$upIcons = ['📄', '📤', '🔄', '🤲', '📦', '🚚', '🛎️', '🎁', '📖', '🗄️', '🏛️', '🛡️', '👨‍💼', '⛲', '👴', '🦸‍♀️', '🏦', '🖼️', '🏛️', '🧐', '👑', 'ℹ️', '🖥️', '💾', '🌌'];
for ($i = 0; $i < 25; $i++) {
    $colorStr = $colors[array_rand($colors)];
    $badges[] = ['id' => 'up_'.$i, 'metric' => 'upload', 'target' => $upTargets[$i], 'name' => $upNames[$i], 'icon' => $upIcons[$i], 'desc' => 'Mengunggah '.$upTargets[$i].' Materi E-Library', 'color' => $colorStr, 'raw_color' => explode('-', explode(' ', $colorStr)[0])[1]];
}

// 6. Downloaded (Popularity) Badges (25 Badges)
$downTargets = [1, 5, 10, 25, 50, 100, 200, 300, 400, 500, 750, 1000, 1500, 2000, 2500, 3000, 4000, 5000, 7500, 10000, 15000, 20000, 25000, 50000, 100000];
$downNames = ['Dilirk Orang', 'Lumayan Berguna', 'Mulai Terkenal', 'Banyak Dicari', 'Materi Favorit', 'Viral Lokal', 'Trending Kampus', 'Bintang Kelas', 'Selebgram Kampus', 'Influencer Edukasi', 'Dosen Pujaan', 'Idola Mahasiswa', 'Materi Legend', 'Kitab Suci', 'Harta Karun', 'Peninggalan Pusaka', 'Buku Sakti', 'Kunci Jawaban Dewa', 'Bocor Halus', 'Viral Nasional', 'Mega Bintang', 'Superstar', 'Global Icon', 'Pusat Semesta', 'Legenda Abadi'];
$downIcons = ['👀', '👍', '🌟', '🔍', '❤️', '🔥', '📈', '⭐', '📸', '📱', '😍', '🤩', '📜', '📖', '💎', '🏺', '🪄', '🗝️', '💧', '🎇', '🌠', '🎸', '🌍', '🌌', '🗿'];
for ($i = 0; $i < 25; $i++) {
    $colorStr = $colors[array_rand($colors)];
    $badges[] = ['id' => 'down_'.$i, 'metric' => 'download', 'target' => $downTargets[$i], 'name' => $downNames[$i], 'icon' => $downIcons[$i], 'desc' => 'Materimu di-download '.$downTargets[$i].' kali', 'color' => $colorStr, 'raw_color' => explode('-', explode(' ', $colorStr)[0])[1]];
}

return [
    'categories' => [
        'forum' => [
            'Diskusi Umum', 'Tugas', 'Jawaban UTS/UAS', 'Catatan', 'Skripsi/Tugas Akhir', 
            'Lomba & Kompetisi', 'Beasiswa', 'Magang & Karir', 'Lowongan Kerja', 
            'Kepanitiaan', 'Organisasi', 'Tips & Trik', 'Pengumuman', 'Hiburan / OOT', 
            'Teknologi', 'Bahasa', 'Sains', 'Bisnis & Ekonomi', 'Seni & Desain', 'Kesehatan'
        ],
        'library' => [
            'Buku/Diktat', 'Slide/PPT', 'Modul Praktikum', 'Jurnal Ilmiah', 'Soal Ujian Tahun Lalu', 
            'Catatan Tulis Tangan', 'Laporan Praktikum', 'Skripsi/Tesis/Disertasi', 
            'Video Edukasi', 'Audio/Podcast', 'Dokumen Lainnya'
        ]
    ],
    'faculties' => [
        'Fakultas MIPA', 'Fakultas Teknik', 'Fakultas Kedokteran', 'Fakultas Ekonomi & Bisnis', 
        'Fakultas Ilmu Komputer', 'Fakultas Hukum', 'Fakultas Ilmu Sosial & Ilmu Politik', 
        'Fakultas Keguruan & Ilmu Pendidikan', 'Fakultas Ilmu Budaya', 'Fakultas Pertanian', 
        'Fakultas Peternakan', 'Fakultas Psikologi', 'Fakultas Farmasi', 'Fakultas Kehutanan', 
        'Fakultas Kesehatan Masyarakat'
    ],
    'badges' => $badges,
];
