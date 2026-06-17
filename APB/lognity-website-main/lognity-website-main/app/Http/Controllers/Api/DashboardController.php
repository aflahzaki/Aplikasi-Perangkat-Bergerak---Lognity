<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Request as RequestModel;
use App\Models\Ebook;
use App\Models\Interaction;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();

        // Data yang berhubungan dengan User saat ini
        $myRequests = RequestModel::where('user_id', $user->user_id)->count();
        $myAnswers = Interaction::where('user_id', $user->user_id)->where('type', 'Answer')->count();

        // Data Global untuk Dashboard
        $totalUsers = User::count();
        $totalForums = RequestModel::count();
        $totalEbooks = Ebook::count();

        // 5 Forum terbaru
        $recentForums = RequestModel::with('user')
            ->latest()
            ->take(5)
            ->get();

        $limitsConfig = User::LIMITS[$user->current_level] ?? User::LIMITS['Maba'];
        $reqUsed = $user->requests()->whereDate('created_at', \Carbon\Carbon::today())->count();
        $intUsed = $user->interactions()->whereDate('created_at', \Carbon\Carbon::today())->count();

        return response()->json([
            'user_stats' => [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'email' => $user->email,
                'profil_url' => $user->profil_url,
                'points' => $user->points,
                'level' => $user->current_level,
                'my_requests_count' => $myRequests,
                'my_answers_count' => $myAnswers,
                'limits' => [
                    'request_limit' => $limitsConfig['request'],
                    'request_used' => $reqUsed,
                    'interaction_limit' => $limitsConfig['interaction'],
                    'interaction_used' => $intUsed,
                ],
            ],
            'global_stats' => [
                'total_users' => $totalUsers,
                'total_forums' => $totalForums,
                'total_ebooks' => $totalEbooks,
            ],
            'recent_forums' => $recentForums,
        ]);
    }
}
