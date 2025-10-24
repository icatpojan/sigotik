<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search across all modules
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        $results = [];

        if ($type === 'all' || $type === 'bbm') {
            $bbmResults = BbmKapaltrans::with('kapal')
                ->where('nomor_surat', 'like', "%{$query}%")
                ->orWhere('lokasi_surat', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            $results['bbm'] = $bbmResults;
        }

        if ($type === 'all' || $type === 'kapal') {
            $kapalResults = MKapal::with('upt')
                ->where('nama_kapal', 'like', "%{$query}%")
                ->orWhere('code_kapal', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            $results['kapal'] = $kapalResults;
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
