<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request)
    {
        $user = $request->user();

        // Get statistics based on user role and UPT
        $stats = [
            'total_bbm_transactions' => BbmKapaltrans::count(),
            'total_kapals' => MKapal::count(),
            'total_upts' => MUpt::count(),
            'pending_approvals' => BbmKapaltrans::where('status_trans', 0)->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
