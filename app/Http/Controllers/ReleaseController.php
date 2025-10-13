<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\MUpt;

class ReleaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kapals = MKapal::all();
        $upts = MUpt::all();

        return view('release.index', compact('kapals', 'upts'));
    }

    /**
     * Get BBM kapal trans data via AJAX
     */
    public function getBbmKapaltrans(Request $request)
    {
        $query = BbmKapaltrans::with(['kapal']);

        // Search functionality
        if ($request->has('search') && !empty(trim($request->search))) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('lokasi_surat', 'like', "%{$search}%")
                    ->orWhere('nama_nahkoda', 'like', "%{$search}%")
                    ->orWhere('nama_kkm', 'like', "%{$search}%")
                    ->orWhere('nama_an', 'like', "%{$search}%")
                    ->orWhere('nomor_nota', 'like', "%{$search}%")
                    ->orWhereHas('kapal', function ($kapalQuery) use ($search) {
                        $kapalQuery->where('nama_kapal', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by kapal
        if ($request->has('kapal') && !empty($request->kapal)) {
            $query->where('kapal_code', $request->kapal);
        }

        // Filter by status trans
        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $query->where('status_trans', $request->status);
        }

        // Filter by jenis transport
        if ($request->has('jenis_transport') && $request->jenis_transport !== '' && $request->jenis_transport !== null) {
            $query->where('jenis_tranport', $request->jenis_transport);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('tanggal_surat', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('tanggal_surat', '<=', $request->date_to);
        }

        // Sort by tanggal_surat desc (newest first)
        $query->orderBy('tanggal_surat', 'desc');

        // Per page parameter
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $bbmKapaltrans = $query->paginate($perPage);

        // Add can_release flag for each item
        $items = $bbmKapaltrans->items();
        foreach ($items as $item) {
            $item->can_release = auth()->user()->conf_group_id == 1;
        }

        return response()->json([
            'success' => true,
            'bbm_kapaltrans' => $items,
            'pagination' => [
                'current_page' => $bbmKapaltrans->currentPage(),
                'last_page' => $bbmKapaltrans->lastPage(),
                'per_page' => $bbmKapaltrans->perPage(),
                'total' => $bbmKapaltrans->total(),
                'from' => $bbmKapaltrans->firstItem(),
                'to' => $bbmKapaltrans->lastItem(),
                'has_more_pages' => $bbmKapaltrans->hasMorePages(),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(BbmKapaltrans $bbmKapaltrans)
    {
        return response()->json([
            'success' => true,
            'bbm_kapaltrans' => $bbmKapaltrans->load(['kapal'])
        ]);
    }

    /**
     * Release BBM kapal trans data
     */
    public function release(Request $request)
    {
        try {
            // Check if user has permission (only admin can release)
            if (auth()->user()->conf_group_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini'
                ], 403);
            }

            $transId = $request->input('trans_id');

            if (!$transId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID transaksi tidak valid'
                ], 400);
            }

            // Find the BBM kapal trans record
            $bbmKapaltrans = BbmKapaltrans::find($transId);

            if (!$bbmKapaltrans) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data transaksi tidak ditemukan'
                ], 404);
            }

            // Get file upload info
            $statusUpload = $bbmKapaltrans->status_upload;
            $fileUpload = $bbmKapaltrans->file_upload;

            // Delete physical file if exists
            if ($fileUpload && $statusUpload == 1) {
                $filePath = public_path('dokumen/dokumen_up_ba/' . $fileUpload);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Update database - reset upload status and file name
            $bbmKapaltrans->update([
                'status_upload' => 0,
                'file_upload' => ''
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di-release'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat me-release data: ' . $e->getMessage()
            ], 500);
        }
    }
}
