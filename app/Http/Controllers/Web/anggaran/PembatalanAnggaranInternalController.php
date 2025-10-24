<?php

namespace App\Http\Controllers\Web\anggaran;

use Illuminate\Http\Request;
use App\Models\BbmAnggaranUpt;
use App\Models\MUpt;
use App\Models\ConfUser;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
class PembatalanAnggaranInternalController extends Controller
{
    public function index()
    {
        return view('anggaran.pembatalan-anggaran-internal');
    }

    public function getData(Request $request)
    {
        $query = BbmAnggaranUpt::with(['upt', 'userInput', 'userApp'])
            ->where('statusperubahan', 1) // Hanya yang sudah disetujui
            ->orderBy('tanggal_trans', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'LIKE', "%{$search}%")
                    ->orWhere('keterangan', 'LIKE', "%{$search}%")
                    ->orWhere('nominal', 'LIKE', "%{$search}%")
                    ->orWhereHas('upt', function ($uptQuery) use ($search) {
                        $uptQuery->where('nama', 'LIKE', "%{$search}%");
                    });
            });
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function getViewData($id)
    {
        $data = BbmAnggaranUpt::with(['upt', 'userInput', 'userApp'])->find($id);
        return response()->json(['data' => $data]);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        try {
            $anggaranUpt = BbmAnggaranUpt::find($request->id);

            if (!$anggaranUpt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anggaran internal tidak ditemukan'
                ], 404);
            }

            // Cek apakah data bisa dibatalkan
            if ($anggaranUpt->statusperubahan != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak dapat dibatalkan'
                ], 400);
            }

            $user = Auth::user();

            $anggaranUpt->update([
                'statusperubahan' => 2 // Dibatalkan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Anggaran internal berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan anggaran internal: ' . $e->getMessage()
            ], 500);
        }
    }
}
