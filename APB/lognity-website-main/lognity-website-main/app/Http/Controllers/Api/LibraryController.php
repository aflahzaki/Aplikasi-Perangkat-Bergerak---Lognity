<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ebook;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = Ebook::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $ebooks = $query->latest()->paginate(10);

        // Menambahkan URL lengkap untuk cover dan file PDF
        $ebooks->getCollection()->transform(function ($ebook) {
            $ebook->cover_url = $ebook->cover_path ? asset('storage/' . $ebook->cover_path) : null;
            $ebook->file_url = $ebook->file_path ? asset('storage/' . $ebook->file_path) : null;
            return $ebook;
        });

        return response()->json($ebooks);
    }

    public function show($id)
    {
        $ebook = Ebook::find($id);

        if (!$ebook) {
            return response()->json(['message' => 'Ebook tidak ditemukan'], 404);
        }

        $ebook->cover_url = $ebook->cover_path ? asset('storage/' . $ebook->cover_path) : null;
        $ebook->file_url = $ebook->file_path ? asset('storage/' . $ebook->file_path) : null;

        return response()->json($ebook);
    }
}
