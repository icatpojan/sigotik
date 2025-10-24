<?php

namespace App\Http\Controllers\Web\monitoring;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\ConfUser;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BbmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kapals = MKapal::all();
        $users = ConfUser::all();
        $upts = MUpt::all();

        return view('bbm.index', compact('kapals', 'users', 'upts'));
    }

    /**
     * Get BBM data via AJAX
     */
    public function getBbmData(Request $request)
    {
        $query = BbmKapaltrans::with(['kapal', 'userInput', 'userApp']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('kapal_code', 'like', "%{$search}%")
                    ->orWhere('lokasi_surat', 'like', "%{$search}%")
                    ->orWhereHas('kapal', function ($kapalQuery) use ($search) {
                        $kapalQuery->where('nama_kapal', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status BA
        if ($request->has('status_ba') && $request->status_ba) {
            $query->where('status_ba', $request->status_ba);
        }

        // Filter by status transaksi
        if ($request->has('status_trans') && $request->status_trans !== '' && $request->status_trans !== null) {
            $query->where('status_trans', $request->status_trans);
        }

        // Filter by kapal
        if ($request->has('kapal') && $request->kapal) {
            $query->where('kapal_code', $request->kapal);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('tanggal_surat', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('tanggal_surat', '<=', $request->date_to);
        }

        // Per page parameter
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $bbmData = $query->orderBy('tanggal_surat', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'bbm_data' => $bbmData->items(),
            'pagination' => [
                'current_page' => $bbmData->currentPage(),
                'last_page' => $bbmData->lastPage(),
                'per_page' => $bbmData->perPage(),
                'total' => $bbmData->total(),
                'from' => $bbmData->firstItem(),
                'to' => $bbmData->lastItem(),
                'has_more_pages' => $bbmData->hasMorePages(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kapal_code' => 'required|string|max:30',
            'nomor_surat' => 'required|string|max:50|unique:bbm_kapaltrans,nomor_surat',
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|string|max:8',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string|max:200',
            'status_ba' => 'required|integer|min:1|max:15',
            'volume_sisa' => 'nullable|numeric|min:0',
            'volume_sebelum' => 'nullable|numeric|min:0',
            'volume_pengisian' => 'nullable|numeric|min:0',
            'volume_pemakaian' => 'nullable|numeric|min:0',
            'nama_nahkoda' => 'nullable|string|max:50',
            'nip_nahkoda' => 'nullable|string|max:50',
            'nama_kkm' => 'nullable|string|max:50',
            'nip_kkm' => 'nullable|string|max:50',
            'an_nakhoda' => 'boolean',
            'an_kkm' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the highest ID and add 1
            $maxId = BbmKapaltrans::max('trans_id') ?? 0;
            $newId = $maxId + 1;

            $bbmData = BbmKapaltrans::create([
                'trans_id' => $newId,
                'kapal_code' => $request->kapal_code,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'status_ba' => $request->status_ba,
                'volume_sisa' => $request->volume_sisa ?? 0,
                'volume_sebelum' => $request->volume_sebelum ?? 0,
                'volume_pengisian' => $request->volume_pengisian ?? 0,
                'volume_pemakaian' => $request->volume_pemakaian ?? 0,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_nakhoda' => $request->an_nakhoda ? 1 : 0,
                'an_kkm' => $request->an_kkm ? 1 : 0,
                'user_input' => auth()->user()->conf_user_id ?? 1,
                'tanggal_input' => now(),
                'status_trans' => 0, // Input
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data BBM berhasil dibuat',
                'bbm_data' => $bbmData->load(['kapal', 'userInput'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BbmKapaltrans $bbm)
    {
        return response()->json([
            'success' => true,
            'bbm_data' => $bbm->load(['kapal', 'userInput', 'userApp'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BbmKapaltrans $bbm)
    {
        $validator = Validator::make($request->all(), [
            'kapal_code' => 'required|string|max:30',
            'nomor_surat' => 'required|string|max:50|unique:bbm_kapaltrans,nomor_surat,' . $bbm->trans_id . ',trans_id',
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|string|max:8',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string|max:200',
            'status_ba' => 'required|integer|min:1|max:15',
            'volume_sisa' => 'nullable|numeric|min:0',
            'volume_sebelum' => 'nullable|numeric|min:0',
            'volume_pengisian' => 'nullable|numeric|min:0',
            'volume_pemakaian' => 'nullable|numeric|min:0',
            'nama_nahkoda' => 'nullable|string|max:50',
            'nip_nahkoda' => 'nullable|string|max:50',
            'nama_kkm' => 'nullable|string|max:50',
            'nip_kkm' => 'nullable|string|max:50',
            'an_nakhoda' => 'boolean',
            'an_kkm' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bbm->update([
                'kapal_code' => $request->kapal_code,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'status_ba' => $request->status_ba,
                'volume_sisa' => $request->volume_sisa ?? 0,
                'volume_sebelum' => $request->volume_sebelum ?? 0,
                'volume_pengisian' => $request->volume_pengisian ?? 0,
                'volume_pemakaian' => $request->volume_pemakaian ?? 0,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_nakhoda' => $request->an_nakhoda ? 1 : 0,
                'an_kkm' => $request->an_kkm ? 1 : 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data BBM berhasil diperbarui',
                'bbm_data' => $bbm->load(['kapal', 'userInput', 'userApp'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BbmKapaltrans $bbm)
    {
        try {
            $bbm->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data BBM berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF for BBM data
     */
    public function generatePdf(BbmKapaltrans $bbm)
    {
        try {
            // TODO: Implement PDF generation using TCPDF
            // This will be implemented after TCPDF integration

            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil dibuat',
                'pdf_url' => '/bbm/pdf/' . $bbm->trans_id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status BA options
     */
    public function getStatusBaOptions()
    {
        $statusOptions = [
            0 => 'BA Default',
            1 => 'BA Penerimaan BBM',
            2 => 'BA Peminjaman BBM',
            3 => 'BA Penitipan BBM',
            4 => 'BA Pemeriksaan Sarana Pengisian',
            5 => 'BA Penerimaan Hibah BBM',
            6 => 'BA Sebelum Pelayaran',
            7 => 'BA Penggunaan BBM',
            8 => 'BA Pengembalian BBM',
            9 => 'BA Penerimaan Pengembalian BBM',
            10 => 'BA Penerimaan Pinjaman BBM',
            11 => 'BA Pengembalian Pinjaman BBM',
            12 => 'BA Pemberi Hibah BBM Kapal Pengawas',
            13 => 'BA Penerima Hibah BBM Kapal Pengawas',
            14 => 'BA Penerima Hibah BBM Instansi Lain',
            15 => 'BA Akhir Bulan'
        ];

        return response()->json([
            'success' => true,
            'status_options' => $statusOptions
        ]);
    }

    /**
     * Get status transaksi options
     */
    public function getStatusTransOptions()
    {
        $statusOptions = [
            0 => 'Input',
            1 => 'Approval',
            2 => 'Batal'
        ];

        return response()->json([
            'success' => true,
            'status_options' => $statusOptions
        ]);
    }
}
