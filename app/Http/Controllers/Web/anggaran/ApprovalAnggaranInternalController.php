<?php

namespace App\Http\Controllers\Web\anggaran;

use Illuminate\Http\Request;
use App\Models\BbmAnggaranUpt;
use App\Models\MUpt;
use App\Models\ConfUser;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ApprovalAnggaranInternalController extends Controller
{
    public function index()
    {
        return view('anggaran.approval-anggaran-internal');
    }

    public function getData(Request $request)
    {
        $query = BbmAnggaranUpt::with(['upt', 'userInput'])
            ->whereIn('statusperubahan', [0, 2]) // Belum disetujui atau dibatalkan
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

    public function approve(Request $request)
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

            // Cek apakah data bisa diapprove
            if ($anggaranUpt->statusperubahan != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah tidak dapat diapprove'
                ], 400);
            }

            $user = Auth::user();

            $anggaranUpt->update([
                'statusperubahan' => 1, // Disetujui
                'user_app' => $user->username,
                'tanggal_app' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Anggaran internal berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui anggaran internal: ' . $e->getMessage()
            ], 500);
        }
    }
}
