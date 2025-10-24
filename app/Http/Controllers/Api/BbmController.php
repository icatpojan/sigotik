<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\ConfUser;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="BBM Management",
 *     description="API endpoints for BBM (Bahan Bakar Minyak) management"
 * )
 */
class BbmController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/bbm",
     *     tags={"BBM Management"},
     *     summary="Get BBM list",
     *     description="Retrieve paginated list of BBM transactions with filtering options",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (10, 20, 50, 100)",
     *         required=false,
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for nomor surat, kapal, or lokasi",
     *         required=false,
     *         @OA\Schema(type="string", example="BA/001")
     *     ),
     *     @OA\Parameter(
     *         name="status_ba",
     *         in="query",
     *         description="Filter by status BA (1-15)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="status_trans",
     *         in="query",
     *         description="Filter by transaction status (0=Input, 1=Approval, 2=Batal)",
     *         required=false,
     *         @OA\Schema(type="integer", example=0)
     *     ),
     *     @OA\Parameter(
     *         name="kapal",
     *         in="query",
     *         description="Filter by kapal code",
     *         required=false,
     *         @OA\Schema(type="string", example="KAP001")
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter from date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter to date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="BBM list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="trans_id", type="integer", example=1),
     *                     @OA\Property(property="kapal_code", type="string", example="KAP001"),
     *                     @OA\Property(property="nomor_surat", type="string", example="BA/001/2024"),
     *                     @OA\Property(property="tanggal_surat", type="string", format="date", example="2024-01-15"),
     *                     @OA\Property(property="jam_surat", type="string", example="08:00:00"),
     *                     @OA\Property(property="zona_waktu_surat", type="string", example="WIB"),
     *                     @OA\Property(property="lokasi_surat", type="string", example="Pelabuhan Jakarta"),
     *                     @OA\Property(property="status_ba", type="integer", example=1),
     *                     @OA\Property(property="volume_sisa", type="number", format="float", example=1000.50),
     *                     @OA\Property(property="volume_sebelum", type="number", format="float", example=800.00),
     *                     @OA\Property(property="volume_pengisian", type="number", format="float", example=200.50),
     *                     @OA\Property(property="volume_pemakaian", type="number", format="float", example=0.00),
     *                     @OA\Property(property="nama_nahkoda", type="string", example="John Doe"),
     *                     @OA\Property(property="nip_nahkoda", type="string", example="123456789"),
     *                     @OA\Property(property="nama_kkm", type="string", example="Jane Smith"),
     *                     @OA\Property(property="nip_kkm", type="string", example="987654321"),
     *                     @OA\Property(property="an_nakhoda", type="integer", example=1),
     *                     @OA\Property(property="an_kkm", type="integer", example=0),
     *                     @OA\Property(property="status_trans", type="integer", example=0),
     *                     @OA\Property(
     *                         property="kapal",
     *                         type="object",
     *                         @OA\Property(property="nama_kapal", type="string", example="Kapal Patroli 001"),
     *                         @OA\Property(property="code_kapal", type="string", example="KAP001")
     *                     ),
     *                     @OA\Property(
     *                         property="user_input",
     *                         type="object",
     *                         @OA\Property(property="nama_lengkap", type="string", example="Admin User")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=20),
     *                 @OA\Property(property="has_more_pages", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = BbmKapaltrans::with(['kapal', 'userInput', 'userApp']);

        // Role-based filtering
        if ($user->conf_group_id != 1) { // Jika bukan admin
            // Ambil kapal yang terkait dengan user melalui sys_user_kapal
            $userKapalIds = DB::table('sys_user_kapal')
                ->where('conf_user_id', $user->conf_user_id)
                ->pluck('m_kapal_id')
                ->toArray();

            if (!empty($userKapalIds)) {
                // Filter BBM berdasarkan kapal yang dimiliki user
                $query->whereHas('kapal', function ($kapalQuery) use ($userKapalIds) {
                    $kapalQuery->whereIn('m_kapal_id', $userKapalIds);
                });
            } else {
                // Jika user tidak memiliki kapal, return empty
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 20,
                        'total' => 0,
                        'from' => null,
                        'to' => null,
                        'has_more_pages' => false,
                    ]
                ]);
            }
        }

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
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $bbmData = $query->orderBy('tanggal_surat', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $bbmData->items(),
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
     * Store BBM data for mobile
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
                'data' => $bbmData->load(['kapal', 'userInput'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show BBM data for mobile
     */
    public function show(BbmKapaltrans $bbm)
    {
        return response()->json([
            'success' => true,
            'data' => $bbm->load(['kapal', 'userInput', 'userApp'])
        ]);
    }

    /**
     * Update BBM data for mobile
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
                'data' => $bbm->load(['kapal', 'userInput', 'userApp'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete BBM data for mobile
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
     * Generate PDF for mobile
     */
    public function generatePdf(BbmKapaltrans $bbm)
    {
        try {
            // TODO: Implement PDF generation using TCPDF
            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil dibuat',
                'data' => [
                    'pdf_url' => '/bbm/pdf/' . $bbm->trans_id,
                    'download_url' => url('/bbm/pdf/' . $bbm->trans_id)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload document for mobile
     */
    public function uploadDocument(Request $request, BbmKapaltrans $bbm)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'document_type' => 'required|string|in:ba_document,supporting_document'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bbm-documents/' . $bbm->trans_id, $filename, 'public');

            // Update BBM record with file path
            $bbm->update([
                'file_upload' => $filename,
                'status_upload' => 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupload',
                'data' => [
                    'filename' => $filename,
                    'path' => $path,
                    'url' => url('storage/' . $path)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status BA options for mobile
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
            'data' => $statusOptions
        ]);
    }

    /**
     * Get status transaksi options for mobile
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
            'data' => $statusOptions
        ]);
    }
}
