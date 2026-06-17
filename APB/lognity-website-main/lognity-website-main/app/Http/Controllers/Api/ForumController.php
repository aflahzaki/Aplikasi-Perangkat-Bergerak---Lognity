<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\Interaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    // Mengambil daftar forum (request)
    public function index(Request $request)
    {
        $query = RequestModel::with('user')->withCount('answers');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        if ($request->has('faculty')) {
            $query->where('faculty', $request->faculty);
        }

        if ($request->get('feed') === 'following') {
            $userId = $request->user()->user_id;
            $followingIds = \Illuminate\Support\Facades\DB::table('follows')
                ->where('follower_id', $userId)
                ->pluck('following_id');
            $query->whereIn('user_id', $followingIds);
        }

        $sort = $request->get('sort', 'latest');
        if ($sort === 'popular') {
            $query->orderBy('upvotes_count', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // SmartSearch bisa diimplementasikan nanti, gunakan simple search dulu
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(10);
        $userId = $request->user()->user_id;

        $requests->getCollection()->transform(function ($req) use ($userId) {
            $req->is_upvoted = \App\Models\Interaction::where('request_id', $req->request_id)
                ->where('user_id', $userId)
                ->where('type', 'Upvote')
                ->exists();
            return $req;
        });

        return response()->json($requests);
    }

    // Mengambil detail forum beserta komentar
    public function show(\Illuminate\Http\Request $request, $id)
    {
        $forum = RequestModel::with(['user', 'answers.user', 'answers.material'])->find($id);

        if (!$forum) {
            return response()->json(['message' => 'Forum tidak ditemukan'], 404);
        }

        $response = $forum->toArray();
        $userId = $request->user()->user_id;
        $isAdmin = $request->user()->isAdmin();
        
        $response['is_owner'] = $userId === $forum->user_id;
        $response['can_delete'] = $userId === $forum->user_id || $isAdmin;
        $response['is_upvoted'] = \App\Models\Interaction::where('request_id', $id)
            ->where('user_id', $userId)
            ->where('type', 'Upvote')
            ->exists();

        // Map answers to include is_owner
        if (isset($response['answers'])) {
            foreach ($response['answers'] as &$answer) {
                $answer['is_owner'] = $userId === $answer['user_id'];
                $answer['can_delete'] = $userId === $answer['user_id'] || $isAdmin;
            }
        }

        return response()->json($response);
    }

    // Membuat request baru
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->hasReachedLimit('request')) {
            return response()->json(['message' => 'Batas request harian Anda sudah habis.'], 403);
        }

        $request->validate([
            'description' => 'required|min:10|max:500',
            'category'    => 'required',
            'faculty'     => 'nullable|string|max:50',
            'course_name' => 'nullable|string|max:50',
            'attachment'  => 'nullable|file|max:5120',
        ]);

        // Catatan: ContentModerator check harusnya ada disini (disesuaikan dgn Lognity)
        $check = \App\Services\ContentModerator::check($request->description);
        if ($check['is_toxic']) {
            $user->decrement('points', 25);
            return response()->json(['message' => 'Konten DITOLAK! Terdeteksi unsur: ' . $check['category'] . '. Poin Anda dikurangi 25.'], 403);
        }

        $filePath = $request->hasFile('attachment') 
            ? $request->file('attachment')->store('requests_attachments', 'public') 
            : null;

        $newRequest = RequestModel::create([
            'user_id'     => $user->user_id,
            'description' => $request->description,
            'faculty'     => $request->faculty,
            'course_name' => $request->course_name,
            'category'    => $request->category,
            'attachment_file' => $filePath,
            'status'      => 'Open',
        ]);

        return response()->json(['message' => 'Request berhasil dibuat!', 'data' => $newRequest], 201);
    }

    public function update(Request $request, $id)
    {
        $forum = RequestModel::find($id);

        if (!$forum) return response()->json(['message' => 'Forum tidak ditemukan'], 404);
        if ($forum->user_id !== $request->user()->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'description' => 'required|min:10|max:500',
            'category'    => 'required',
            'faculty'     => 'nullable|string|max:50',
            'course_name' => 'nullable|string|max:50',
            'attachment'  => 'nullable|file|max:5120',
        ]);

        $check = \App\Services\ContentModerator::check($request->description);
        if ($check['is_toxic']) {
            return response()->json(['message' => 'Konten DITOLAK! Terdeteksi unsur: ' . $check['category']], 403);
        }

        if ($request->hasFile('attachment')) {
            if ($forum->attachment_file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($forum->attachment_file);
            }
            $forum->attachment_file = $request->file('attachment')->store('requests_attachments', 'public');
        }

        $forum->update([
            'description' => $request->description,
            'faculty' => $request->faculty,
            'course_name' => $request->course_name,
            'category' => $request->category,
        ]);

        return response()->json(['message' => 'Request berhasil diperbarui!', 'data' => $forum]);
    }

    public function destroy(Request $request, $id)
    {
        $forum = RequestModel::find($id);

        if (!$forum) return response()->json(['message' => 'Forum tidak ditemukan'], 404);
        if ($forum->user_id !== $request->user()->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($forum->attachment_file) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($forum->attachment_file);
        }
        $forum->delete();

        return response()->json(['message' => 'Request berhasil dihapus']);
    }

    // Fitur Upvote
    public function upvote(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->canUpvote() || $user->points < 500) {
            return response()->json(['message' => 'Level Mahasiswa Baru / Poin kurang tidak bisa Upvote!'], 403);
        }

        $req = RequestModel::find($id);
        if (!$req) return response()->json(['message' => 'Not found'], 404);

        $existingUpvote = Interaction::where('user_id', $user->user_id)
            ->where('request_id', $id)
            ->where('type', 'Upvote')
            ->first();

        if ($existingUpvote) {
            $existingUpvote->delete();
            $req->decrement('upvotes_count');
            return response()->json(['message' => 'Upvote dibatalkan', 'upvotes_count' => $req->upvotes_count]);
        } else {
            Interaction::create([
                'user_id' => $user->user_id,
                'request_id' => $id,
                'type' => 'Upvote'
            ]);
            $req->increment('upvotes_count');

            $owner = User::find($req->user_id);
            if($owner && $owner->user_id !== $user->user_id) {
                $owner->increment('points', 5);
            }

            return response()->json(['message' => 'Upvote berhasil ditambahkan', 'upvotes_count' => $req->upvotes_count]);
        }
    }

    // Fitur Menambahkan Komentar (Answer)
    public function storeAnswer(Request $request, $id)
    {
        $user = $request->user();

        if ($user->hasReachedLimit('interaction')) {
            return response()->json(['message' => 'Kuota interaksi harian Anda habis.'], 403);
        }

        $request->validate([
            'content' => 'required|min:5|max:1000',
            'file'    => 'nullable|file|max:10240',
        ]);

        $check = \App\Services\ContentModerator::check($request->content);
        if ($check['is_toxic']) {
            $user->decrement('points', 25);
            return response()->json(['message' => 'Komentar DITOLAK! Terdeteksi unsur: ' . $check['category'] . '.'], 403);
        }

        $materialId = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('materials', 'public');
            
            $material = \App\Models\Material::create([
                'uploader_id' => $user->user_id,
                'related_request_id' => $id,
                'title' => 'Jawaban untuk Request #' . $id,
                'description' => $request->content,
                'file_path' => $filePath,
                'tags' => 'jawaban',
            ]);
            
            $materialId = $material->material_id;
            
            // Tambah Poin Upload (+20)
            $user->increment('points', 20);
        }

        $interaction = Interaction::create([
            'user_id' => $user->user_id,
            'request_id' => $id,
            'material_id' => $materialId,
            'type' => 'Answer',
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Jawaban terkirim!', 'data' => $interaction], 201);
    }

    // Update Jawaban
    public function updateAnswer(Request $request, $id)
    {
        $interaction = Interaction::find($id);

        if (!$interaction) return response()->json(['message' => 'Jawaban tidak ditemukan'], 404);
        if ($interaction->user_id !== $request->user()->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|min:5|max:1000',
        ]);

        $check = \App\Services\ContentModerator::check($request->content);
        if ($check['is_toxic']) {
            return response()->json(['message' => 'Komentar DITOLAK! Terdeteksi unsur: ' . $check['category'] . '.'], 403);
        }

        $interaction->update(['content' => $request->content]);

        // Note: Untuk simplifikasi, fitur update file lampiran pada jawaban tidak disupport di API ini, hanya teksnya.

        return response()->json(['message' => 'Jawaban berhasil diperbarui']);
    }

    // Hapus Jawaban
    public function destroyAnswer(Request $request, $id)
    {
        $interaction = Interaction::find($id);

        if (!$interaction) return response()->json(['message' => 'Jawaban tidak ditemukan'], 404);
        if ($interaction->user_id !== $request->user()->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($interaction->material_id) {
            $material = \App\Models\Material::find($interaction->material_id);
            if ($material) {
                if ($material->file_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($material->file_path);
                }
                $material->delete();
            }
        }

        $interaction->delete();

        return response()->json(['message' => 'Jawaban berhasil dihapus']);
    }

    // Terima Jawaban (Accept Answer)
    public function acceptAnswer(Request $request, $id)
    {
        $interaction = Interaction::with('request')->find($id);
        
        if (!$interaction) {
            return response()->json(['message' => 'Jawaban tidak ditemukan'], 404);
        }

        $forum = $interaction->request;
        
        if ($forum->user_id !== $request->user()->user_id) {
            return response()->json(['message' => 'Hanya pembuat request yang dapat menerima jawaban'], 403);
        }

        if ($interaction->user_id === $request->user()->user_id) {
            return response()->json(['message' => 'Tidak bisa menerima jawaban sendiri'], 400);
        }

        if ($forum->status === 'Resolved') {
            return response()->json(['message' => 'Request ini sudah diselesaikan'], 400);
        }

        $interaction->update(['is_accepted_answer' => true]);
        $forum->update(['status' => 'Resolved']);

        // Beri poin ke penjawab
        $answerer = \App\Models\User::find($interaction->user_id);
        if ($answerer) {
            $answerer->increment('points', 50);
            
            // Recalculate level
            $p = $answerer->points;
            $newLevel = 'Maba';
            if ($p >= 8000) $newLevel = 'Artefak';
            elseif ($p >= 2500) $newLevel = 'Calon';
            elseif ($p >= 500) $newLevel = 'Aktif';

            if ($answerer->current_level !== $newLevel) {
                $answerer->update(['current_level' => $newLevel]);
            }
        }

        return response()->json(['message' => 'Jawaban diterima! Penjawab mendapatkan +50 Poin.']);
    }

    public function report(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:request,interaction,user',
            'target_id' => 'required|integer',
            'reason' => 'required|string|min:3|max:255',
        ]);

        $data = [
            'reporter_id' => $request->user()->user_id,
            'reason' => $request->reason,
            'status' => 'Pending',
        ];

        if ($request->target_type === 'request') {
            $data['target_request_id'] = $request->target_id;
        } elseif ($request->target_type === 'interaction') {
            $data['target_interaction_id'] = $request->target_id;
        } elseif ($request->target_type === 'user') {
            $data['target_user_id'] = $request->target_id;
        }

        \App\Models\Report::create($data);

        return response()->json(['message' => 'Laporan berhasil dikirim ke Admin.'], 201);
    }
}
