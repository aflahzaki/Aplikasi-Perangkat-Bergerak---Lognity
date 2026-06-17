<?php

namespace App\Services;

use Illuminate\Support\Collection;

class SmartSearch
{
    /**
     * Melakukan pencarian pintar (Fuzzy Search) pada Collection.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query awal (Model::query())
     * @param array $columns Kolom yang mau dicari (misal: ['title', 'description'])
     * @param string $keyword Kata kunci pencarian (yang mungkin typo)
     * @return Collection Hasil yang sudah diurutkan berdasarkan kemiripan
     */
    public static function search($query, array $columns, string $keyword)
    {
        if (empty($keyword)) {
            return $query->paginate(10); // Kembalikan normal jika kosong
        }

        $keyword = strtolower($keyword);

        // 1. Ambil Data Secukupnya dari Database (Optimasi: Ambil 200 data terbaru untuk dicek)
        // Kita tidak bisa cek seluruh database jika datanya jutaan, tapi untuk skripsi/tugas ini aman.
        $items = $query->latest()->take(200)->get();

        // 2. Filter & Scoring menggunakan PHP (Levenshtein)
        $filtered = $items->map(function ($item) use ($columns, $keyword) {
            $bestScore = 1000; // Nilai awal (semakin kecil semakin mirip)

            foreach ($columns as $col) {
                $text = strtolower($item->$col ?? '');
                
                // A. Cek jika kata ada di dalam teks (Exact Match) -> Skor 0 (Prioritas Tertinggi)
                if (str_contains($text, $keyword)) {
                    $bestScore = 0;
                    break; 
                }

                // B. Cek Typo (Fuzzy Match)
                // Levenshtein menghitung berapa huruf yang harus diganti agar sama
                // Kita bandingkan keyword dengan setiap kata dalam kalimat (agar lebih akurat)
                $words = explode(' ', $text);
                foreach ($words as $word) {
                    $distance = levenshtein($keyword, $word);
                    if ($distance < $bestScore) {
                        $bestScore = $distance;
                    }
                }
            }

            $item->search_score = $bestScore;
            return $item;
        });

        // 3. Ambil yang skornya rendah (Mirip)
        // Batas toleransi typo: 3 huruf (misal "skripsi" jadi "skripsi" (jarak 0) atau "skripis" (jarak 2))
        $results = $filtered->filter(function ($item) {
            return $item->search_score <= 3; // Ubah angka ini untuk sensitivitas (2-4 bagus)
        })->sortBy('search_score'); // Urutkan yang paling mirip di atas

        // 4. Manual Pagination (Karena kita mengubah Collection, paginate() bawaan SQL tidak bisa dipakai langsung)
        return self::paginate($results, 10);
    }

    // Helper untuk membuat Pagination manual dari Collection
    private static function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (\Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }
}