<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Import Model
use App\Models\Request as ForumRequest; // Alias biar gak bentrok sama Http Request
use App\Models\Material;
use App\Models\Ebook;
use App\Models\User;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        // 1. Ambil Keyword dari URL (?q=...)
        $keyword = $request->query('q');

        if (!$keyword) {
            return response()->json([
                'status' => false,
                'message' => 'Harap masukkan kata kunci pencarian (parameter ?q=)'
            ], 400);
        }

        // 2. Cari di FORUM (Request)
        $forumResults = ForumRequest::with('user:user_id,username,profil') // Optimasi: ambil kolom yg perlu aja
            ->where('description', 'like', "%{$keyword}%")
            ->orWhere('course_name', 'like', "%{$keyword}%")
            ->orWhere('category', 'like', "%{$keyword}%")
            ->latest()
            ->take(5) // Batasi 5 hasil biar ringan
            ->get();

        // 3. Cari di MATERIAL (User Upload)
        $materialResults = Material::with('uploader:user_id,username')
            ->where('title', 'like', "%{$keyword}%")
            ->orWhere('tags', 'like', "%{$keyword}%")
            ->latest()
            ->take(5)
            ->get();

        // 4. Cari di E-LIBRARY (Official)
        $ebookResults = Ebook::where('title', 'like', "%{$keyword}%")
            ->orWhere('author', 'like', "%{$keyword}%")
            ->latest()
            ->take(5)
            ->get();

        // 5. (Opsional) Cari USER
        $userResults = User::where('username', 'like', "%{$keyword}%")
            ->select('user_id', 'username', 'profil', 'current_level') // Privasi: jangan tampilkan email/password
            ->take(3)
            ->get();

        // 6. Return JSON Response
        return response()->json([
            'status' => true,
            'message' => 'Hasil pencarian ditemukan.',
            'query' => $keyword,
            'data' => [
                'forum' => $forumResults,
                'materials' => $materialResults,
                'ebooks' => $ebookResults,
                'users' => $userResults,
            ]
        ], 200);
    }
}