<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ContentModerator
{
    /**
     * Cek apakah teks mengandung konten negatif.
     * Mengembalikan ARRAY: ['is_toxic' => bool, 'category' => string]
     */
    public static function check($text)
    {
        // 1. Tembak API OpenAI Moderation
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/moderations', [
            'input' => $text
        ]);

        // 2. Jika Gagal Koneksi (misal API Key salah/internet mati)
        // Kita anggap aman dulu agar sistem tidak error, atau bisa throw error.
        if ($response->failed()) {
            return ['is_toxic' => false, 'category' => null];
        }

        // 3. Ambil Hasil
        $result = $response->json()['results'][0];

        // 4. Jika Terdeteksi Flagged (Toxic)
        if ($result['flagged'] === true) {
            // Cari kategori pelanggarannya (misal: violence, hate, sexual)
            $categories = $result['categories'];
            $violation = array_keys($categories, true, true); // Ambil key yang true
            
            return [
                'is_toxic' => true,
                'category' => implode(', ', $violation) // Gabungkan jika ada > 1 kategori
            ];
        }

        // 5. Aman
        return ['is_toxic' => false, 'category' => null];
    }
}