<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BaPemeriksaanSaranaPengisianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kapals = MKapal::all();

        return view('ba-pemeriksaan-sarana-pengisian.index', compact('kapals'));
    }

    /**
     * Get BA Pemeriksaan Sarana Pengisian data via AJAX
     */
    public function getData(Request $request)
    {
        $query = BbmKapaltrans::with(['kapal'])
            ->where('status_ba', 4); // BA Pemeriksaan Sarana Pengisian

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

        $data = $query->orderBy('trans_id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'has_more_pages' => $data->hasMorePages(),
            ]
        ]);
    }

    /**
     * Get kapal data for auto-fill
     */
    public function getKapalData(Request $request)
    {
        $kapalId = $request->kapal_id;

        $kapal = MKapal::with('upt')->find($kapalId);

        if (!$kapal) {
            return response()->json([
                'success' => false,
                'message' => 'Kapal tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'code_kapal' => $kapal->code_kapal,
                'alamat_upt' => $kapal->upt ? $kapal->upt->alamat1 : '',
                'zona_waktu_upt' => $kapal->upt ? $kapal->upt->zona_waktu_upt : 'WIB',
                'kota' => $kapal->upt ? $kapal->upt->kota : '',
                'jabatan_petugas' => $kapal->upt ? $kapal->upt->jabatan_petugas : '',
                'nama_petugas' => $kapal->upt ? $kapal->upt->nama_petugas : '',
                'nip_petugas' => $kapal->upt ? $kapal->upt->nip_petugas : '',
                'nama_nakoda' => $kapal->nama_nakoda,
                'nip_nakoda' => $kapal->nip_nakoda,
                'nama_kkm' => $kapal->nama_kkm,
                'nip_kkm' => $kapal->nip_kkm,
            ]
        ]);
    }

    /**
     * Get BA data for linking (BA sebelumnya)
     */
    public function getBaData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kapal_id' => 'required|exists:m_kapal,m_kapal_id',
            'tanggal_surat' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $kapal = MKapal::find($request->kapal_id);
            $tanggalSurat = $request->tanggal_surat;

            // Cek apakah sudah ada BA Pemeriksaan Sarana Pengisian untuk tanggal dan kapal ini
            $existingBa = BbmKapaltrans::where('kapal_code', $kapal->code_kapal)
                ->where('tanggal_surat', $tanggalSurat)
                ->where('status_ba', 4)
                ->first();

            if ($existingBa) {
                return response()->json([
                    'success' => false,
                    'message' => 'BA Pemeriksaan Sarana Pengisian untuk kapal dan tanggal ini sudah ada'
                ], 400);
            }

            // Cari BA sebelumnya (status_ba = 1,2,6,7) yang belum di-link
            $baSebelumnya = BbmKapaltrans::where('kapal_code', $kapal->code_kapal)
                ->where('tanggal_surat', $tanggalSurat)
                ->whereIn('status_ba', [1, 2, 6, 7])
                ->where('link_modul_ba', '')
                ->orderBy('jam_surat', 'desc')
                ->first();

            if ($baSebelumnya) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'link_ba' => $baSebelumnya->nomor_surat,
                        'volume_sisa' => $baSebelumnya->volume_sisa,
                        'keterangan_jenis_bbm' => $baSebelumnya->keterangan_jenis_bbm ?? 'BIO SOLAR',
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'link_ba' => '',
                        'volume_sisa' => 0,
                        'keterangan_jenis_bbm' => 'BIO SOLAR',
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kapal_id' => 'required|exists:m_kapal,m_kapal_id',
            'nomor_surat' => 'required|string|max:50|unique:bbm_kapaltrans,nomor_surat',
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'penyedia' => 'required|string|max:100',
            'jenis_tranport' => 'required|in:1,2,3',
            'status_segel' => 'required_if:jenis_tranport,1|in:1,2',
            'status_flowmeter' => 'required_if:jenis_tranport,2,3|in:1,2',
            'gambar_segel' => 'nullable|image|mimes:jpg,png|max:2048',
            'gambar_flowmeter' => 'nullable|image|mimes:jpg,png|max:2048',
            'kesimpulan' => 'required|in:1,2',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pagkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:30',
            'nip_nahkoda' => 'nullable|string|max:20',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get kapal data
            $kapal = MKapal::find($request->kapal_id);

            // Get the highest ID and add 1
            $maxId = BbmKapaltrans::max('trans_id') ?? 0;
            $newId = $maxId + 1;

            // Handle file uploads
            $gambarSegel = '';
            $gambarFlowmeter = '';

            if ($request->hasFile('gambar_segel')) {
                $file = $request->file('gambar_segel');
                $filename = uniqid() . '-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('dokumen/gambar_ba_sarana'), $filename);
                $gambarSegel = $filename;
            }

            if ($request->hasFile('gambar_flowmeter')) {
                $file = $request->file('gambar_flowmeter');
                $filename = uniqid() . '-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('dokumen/gambar_ba_sarana'), $filename);
                $gambarFlowmeter = $filename;
            }

            // Set default values untuk status berdasarkan jenis transportasi
            $statusSegel = null;
            $statusFlowmeter = null;

            if ($request->jenis_tranport == 1) {
                // Mobil - gunakan status_segel, set status_flowmeter ke 0 (tidak berlaku)
                $statusSegel = $request->status_segel ?? 0;
                $statusFlowmeter = 0; // Tidak berlaku untuk mobil
            } else if ($request->jenis_tranport == 2 || $request->jenis_tranport == 3) {
                // Kapal atau Pengisian Langsung - gunakan status_flowmeter, set status_segel ke 0 (tidak berlaku)
                $statusSegel = 0; // Tidak berlaku untuk kapal/pengisian langsung
                $statusFlowmeter = $request->status_flowmeter ?? 0;
            }

            $ba = BbmKapaltrans::create([
                'trans_id' => $newId,
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'status_ba' => 4, // BA Pemeriksaan Sarana Pengisian
                'jenis_tranport' => $request->jenis_tranport,
                'status_segel' => $statusSegel,
                'gambar_segel' => $gambarSegel,
                'status_flowmeter' => $statusFlowmeter,
                'gambar_flowmeter' => $gambarFlowmeter,
                'kesimpulan' => $request->kesimpulan,
                'penyedia' => $request->penyedia,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => $request->has('an_staf') ? 1 : 0,
                'an_nakhoda' => $request->has('an_nakhoda') ? 1 : 0,
                'an_kkm' => $request->has('an_kkm') ? 1 : 0,
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'status_trans' => 0, // Input
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Pemeriksaan Sarana Pengisian berhasil dibuat',
                'data' => $ba->load('kapal')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($baPemeriksaanSaranaPengisian)
    {
        try {
            $data = BbmKapaltrans::find($baPemeriksaanSaranaPengisian);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Load relasi kapal dengan upt
            $data->load('kapal.upt');

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the record first
        $baPemeriksaanSaranaPengisian = BbmKapaltrans::find($id);

        if (!$baPemeriksaanSaranaPengisian) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        // Check if nomor_surat is being changed
        $nomorSuratRules = 'required|string|max:50';
        if ($request->nomor_surat !== $baPemeriksaanSaranaPengisian->nomor_surat) {
            $nomorSuratRules .= '|unique:bbm_kapaltrans,nomor_surat,' . $baPemeriksaanSaranaPengisian->trans_id . ',trans_id';
        }

        $validator = Validator::make($request->all(), [
            'kapal_id' => 'required|exists:m_kapal,m_kapal_id',
            'nomor_surat' => $nomorSuratRules,
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'penyedia' => 'required|string|max:100',
            'jenis_tranport' => 'required|in:1,2,3',
            'status_segel' => 'required_if:jenis_tranport,1|in:1,2',
            'status_flowmeter' => 'required_if:jenis_tranport,2,3|in:1,2',
            'gambar_segel' => 'nullable|image|mimes:jpg,png|max:2048',
            'gambar_flowmeter' => 'nullable|image|mimes:jpg,png|max:2048',
            'kesimpulan' => 'required|in:1,2',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pagkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:30',
            'nip_nahkoda' => 'nullable|string|max:20',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get kapal data
            $kapal = MKapal::find($request->kapal_id);

            // Set default values untuk status berdasarkan jenis transportasi
            $statusSegel = null;
            $statusFlowmeter = null;

            if ($request->jenis_tranport == 1) {
                // Mobil - gunakan status_segel, set status_flowmeter ke 0 (tidak berlaku)
                $statusSegel = $request->status_segel ?? 0;
                $statusFlowmeter = 0; // Tidak berlaku untuk mobil
            } else if ($request->jenis_tranport == 2 || $request->jenis_tranport == 3) {
                // Kapal atau Pengisian Langsung - gunakan status_flowmeter, set status_segel ke 0 (tidak berlaku)
                $statusSegel = 0; // Tidak berlaku untuk kapal/pengisian langsung
                $statusFlowmeter = $request->status_flowmeter ?? 0;
            }

            // Handle file uploads
            $updateData = [
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'jenis_tranport' => $request->jenis_tranport,
                'status_segel' => $statusSegel,
                'status_flowmeter' => $statusFlowmeter,
                'kesimpulan' => $request->kesimpulan,
                'penyedia' => $request->penyedia,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => $request->has('an_staf') ? 1 : 0,
                'an_nakhoda' => $request->has('an_nakhoda') ? 1 : 0,
                'an_kkm' => $request->has('an_kkm') ? 1 : 0,
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'updated_at' => now(),
            ];

            if ($request->hasFile('gambar_segel')) {
                // Delete old file if exists
                if ($baPemeriksaanSaranaPengisian->gambar_segel && file_exists(public_path('dokumen/gambar_ba_sarana/' . $baPemeriksaanSaranaPengisian->gambar_segel))) {
                    unlink(public_path('dokumen/gambar_ba_sarana/' . $baPemeriksaanSaranaPengisian->gambar_segel));
                }

                $file = $request->file('gambar_segel');
                $filename = uniqid() . '-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('dokumen/gambar_ba_sarana'), $filename);
                $updateData['gambar_segel'] = $filename;
            }

            if ($request->hasFile('gambar_flowmeter')) {
                // Delete old file if exists
                if ($baPemeriksaanSaranaPengisian->gambar_flowmeter && file_exists(public_path('dokumen/gambar_ba_sarana/' . $baPemeriksaanSaranaPengisian->gambar_flowmeter))) {
                    unlink(public_path('dokumen/gambar_ba_sarana/' . $baPemeriksaanSaranaPengisian->gambar_flowmeter));
                }

                $file = $request->file('gambar_flowmeter');
                $filename = uniqid() . '-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('dokumen/gambar_ba_sarana'), $filename);
                $updateData['gambar_flowmeter'] = $filename;
            }

            $baPemeriksaanSaranaPengisian->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Pemeriksaan Sarana Pengisian berhasil diperbarui',
                'data' => $baPemeriksaanSaranaPengisian->load('kapal')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($baPemeriksaanSaranaPengisian)
    {

        try {
            $data = BbmKapaltrans::find($baPemeriksaanSaranaPengisian);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
            $data->delete();

            return response()->json([
                'success' => true,
                'message' => 'BA Pemeriksaan Sarana Pengisian berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete image file from server.
     */
    public function deleteImage(Request $request, $id)
    {
        try {
            $ba = BbmKapaltrans::find($id);

            if (!$ba) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $fieldName = $request->field_name;
            $filename = $request->filename;

            // Validasi field name
            if (!in_array($fieldName, ['gambar_segel', 'gambar_flowmeter'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Field name tidak valid'
                ], 400);
            }

            // Hapus file dari server
            $filePath = public_path('dokumen/gambar_ba_sarana/' . $filename);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Update database - set field ke null
            $ba->update([
                $fieldName => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus dari server'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF for the specified resource.
     * Template telah disesuaikan dengan template CodeIgniter di prooject_ci/application/models/dokumen/Dokumen_cetak.php
     * Function: cetak_ba_pemeriksa_sarana()
     */
    public function generatePdf($id)
    {
        try {
            // Load relationship data
            $baPemeriksaanSaranaPengisian = BbmKapaltrans::find($id);

            if (!$baPemeriksaanSaranaPengisian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Load relasi kapal dengan upt
            $baPemeriksaanSaranaPengisian->load(['kapal.upt']);

            // Get data
            $data = $baPemeriksaanSaranaPengisian;
            $kapal = $data->kapal;
            $upt = $kapal ? $kapal->upt : null;

            // Ensure all required data is available
            if (!$kapal) {
                throw new \Exception('Data kapal tidak ditemukan');
            }
            if (!$upt) {
                throw new \Exception('Data UPT tidak ditemukan');
            }

            // Format date (sesuai template CodeIgniter - menggunakan f_formattanggal)
            $tanggalFormatted = \Carbon\Carbon::parse($data->tanggal_surat)->locale('id')->isoFormat('dddd, D MMMM YYYY');
            $jamFormatted = str_replace(':', '.', $data->jam_surat);

            // Handle "An." prefix
            $anStaf = ($data->an_staf == 1 || $data->an_staf === true) ? 'An. ' : '';
            $anNakhoda = ($data->an_nakhoda == 1 || $data->an_nakhoda === true) ? 'An. ' : '';
            $anKkm = ($data->an_kkm == 1 || $data->an_kkm === true) ? 'An. ' : '';

            // Create TCPDF instance
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(false);
            $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
            $pdf->AddPage('P', 'A4');
            $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
            $pdf->SetFont('');

            // Build HTML content (sesuai template CodeIgniter)
            $html = '<style type="text/css">
                hr.new5 {
                    border: 20px solid green;
                    border-radius: 5px;
                }
            </style>';

            // Header (sesuai template CodeIgniter)
            $html .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border="0">
                    <tr>
                        <td width="17%" align="center">
                            <img align="center" width="120" height="120" src="' . public_path('images/logo-kkp.png') . '" border="0" />
                        </td>
                        <td width="82%" align="center">
                            <font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
                            <font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
                            <font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
                            <font size="12"><b><i>' . strtoupper($upt ? $upt->nama : 'UPT') . '</b></i></font><br>
                            <font size="10">' . ($upt ? $upt->alamat1 : '') . '</font><br>
                            <font size="10">' . ($upt ? $upt->alamat2 : '') . '</font><br>
                            <font size="10">' . ($upt ? $upt->alamat3 : '') . '</font>
                        </td>
                    </tr>
                </table>';

            // Add lines
            $style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
            $pdf->Line(10, 58, 200, 58, $style);
            $style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
            $pdf->Line(10, 60, 200, 60, $style2);

            // Content (sesuai template CodeIgniter)
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td width="100%" align="center">
                            <font size="12"><b><u>BERITA ACARA PEMERIKSAAN SARANA PENGISIAN BBM</b></u></font>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" align="center">
                            <b>Nomor : ' . $data->nomor_surat . '</b><br>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" align="justify">Pada hari ini ' . $tanggalFormatted . ' pukul ' . $jamFormatted . ' ' . $data->zona_waktu_surat . ', bertempat di ' . strtoupper($data->lokasi_surat) . ', kami yang bertanda tangan
                            dibawah ini :
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="20%" align="justify">Nama/Jabatan</td>
                        <td width="2%" align="center">:</td>
                        <td width="3%" align="center">1.</td>
                        <td width="auto" align="justify">' . $data->nama_nahkoda . ' / Nakhoda Kapal Pengawas ' . ($kapal ? $kapal->nama_kapal : '') . '</td>
                    </tr>
                    <tr>
                        <td width="20%" align="justify"></td>
                        <td width="2%" align="center">:</td>
                        <td width="3%" align="center">2.</td>
                        <td width="auto" align="justify">' . $data->nama_kkm . ' / KKM Kapal Pengawas ' . ($kapal ? $kapal->nama_kapal : '') . '</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="20%" align="justify">Alamat</td>
                        <td width="2%" align="center">:</td>
                        <td width="auto" align="justify">' . ($upt ? $upt->alamat1 : '') . '</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100%" align="justify">Menyatakan bahwa telah melakukan pemeriksaan sarana pengisian BBM dengan rincian sebagai berikut :
                        </td>
                    </tr>
                </table>';

            // Determine jenis transport detail with strikethrough (sesuai template CodeIgniter)
            $jenisTransportDetail = '';
            if ($data->jenis_tranport == 1) {
                $jenisTransportDetail = 'Mobil/<strike>Kapal/Pengisian Langsung</strike>';
            } else if ($data->jenis_tranport == 2) {
                $jenisTransportDetail = '<strike>Mobil</strike>/Kapal/<strike>Pengisian Langsung</strike>';
            } else if ($data->jenis_tranport == 3) {
                $jenisTransportDetail = '<strike>Mobil/Kapal</strike>/Pengisian Langsung';
            }

            // Update content to match CodeIgniter template
            $html = str_replace(
                'Menyatakan bahwa telah melakukan pemeriksaan sarana pengisian BBM dengan rincian sebagai berikut :',
                'Menyatakan bahwa telah melakukan pemeriksaan sarana pengisian dan volume BBM dengan menggunakan ' . $jenisTransportDetail . ' milik ' . $data->penyedia . ', sebelum pengisian dilakukan dengan rincian pemeriksaan sebagai berikut :',
                $html
            );

            // Generate pemeriksaan details based on jenis_tranport (sesuai template CodeIgniter)
            $pemeriksaanDetails = '';
            if ($data->jenis_tranport == 1) {
                // Mobil - focus on segel
                $statusSegelText = $data->status_segel == 1 ? 'baik/<strike>rusak</strike>' : '<strike>baik</strike>/rusak';
                $pemeriksaanDetails = '<br><br>
                    <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                        <tr>
                            <td width="8%" align="center"></td>
                            <td width="3%" align="center">1.</td>
                            <td width="auto" align="justify">&nbsp; Segel tutup tangki dalam kondisi ' . $statusSegelText . '.</td>
                        </tr>
                        <tr>
                            <td width="8%" align="center"></td>
                            <td width="3%" align="center">2.</td>
                            <td width="auto" align="justify">&nbsp;<strike> Flowmeter dalam kondisi baik/<strike>rusak</strike>.</strike></td>
                        </tr>
                        <tr>
                            <td width="8%" align="center"></td>
                        </tr>
                        <tr>
                            <td width="12%" align="justify">Kesimpulan</td>
                            <td width="3%" align="center">:</td>
                            <td width="auto" align="justify">Pengisian dapat ' . ($data->kesimpulan == 1 ? 'dilakukan' : 'ditunda sampai dengan tersedianya sarana pengganti.') . '</td>
                        </tr>
                    </table>';
            } else if ($data->jenis_tranport == 2) {
                // Kapal - focus on flowmeter
                $statusFlowmeterText = $data->status_flowmeter == 1 ? 'baik/<strike>rusak</strike>' : '<strike>baik</strike>/rusak';
                $pemeriksaanDetails = '<br><br>
                    <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                        <tr>
                            <td width="8%" align="center"></td>
                            <td width="3%" align="center">1.</td>
                            <td width="auto" align="justify">&nbsp; <strike> Segel tutup tangki dalam kondisi baik/<strike>rusak</strike>.</strike></td>
                        </tr>
                        <tr>
                            <td width="8%" align="center"></td>
                            <td width="3%" align="center">2.</td>
                            <td width="auto" align="justify">&nbsp; Flowmeter dalam kondisi ' . $statusFlowmeterText . '.</td>
                        </tr>
                        <tr>
                            <td width="8%" align="center"></td>
                        </tr>
                        <tr>
                            <td width="12%" align="justify">Kesimpulan</td>
                            <td width="3%" align="center">:</td>
                            <td width="auto" align="justify">Pengisian dapat ' . ($data->kesimpulan == 1 ? 'dilakukan' : 'ditunda sampai dengan tersedianya sarana pengganti.') . '</td>
                        </tr>
                    </table>';
            } else {
                // Pengisian Langsung - focus on flowmeter
                $statusFlowmeterText = $data->status_flowmeter == 1 ? 'baik/<strike>rusak</strike>' : '<strike>baik</strike>/rusak';
                $pemeriksaanDetails = '<br><br>
                    <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                        <tr>
                            <td width="8%" align="center"></td>
                            <td width="3%" align="center">1.</td>
                            <td width="auto" align="justify">&nbsp;<strike> Segel tutup tangki dalam kondisi baik/<strike>rusak</strike>.</strike></td>
                        </tr>
                        <tr>
                            <td width="8%" align="center"></td>
                            <td width="3%" align="center">2.</td>
                            <td width="auto" align="justify">&nbsp;Flowmeter dalam kondisi ' . $statusFlowmeterText . '.</td>
                        </tr>
                        <tr>
                            <td width="8%" align="center"></td>
                        </tr>
                        <tr>
                            <td width="12%" align="justify">Kesimpulan</td>
                            <td width="3%" align="center">:</td>
                            <td width="auto" align="justify">Pengisian dapat ' . ($data->kesimpulan == 1 ? 'dilakukan' : 'ditunda sampai dengan tersedianya sarana pengganti.') . '</td>
                        </tr>
                    </table>';
            }

            $html .= $pemeriksaanDetails;

            // Closing statement (sesuai template CodeIgniter)
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td width="100%" align="justify">Demikian Berita Acara Pemeriksaan Sarana Pengisian ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.
                        </td>
                    </tr>
                </table>';

            // Footer signatures - sesuai template CodeIgniter
            $html .= '<br></br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border="0">
                    <tr>
                        <td width="40%" align="center">
                            <b>' . $anKkm . ' KKM KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_kkm . '</u></b><br>
                            <b>NIP. ' . $data->nip_kkm . '</b>
                        </td>
                        <td width="20%" align="center"></td>
                        <td width="40%" align="center">
                            <b>Penyedia/Pengirim BBM</b><br>
                            <b>' . $data->penyedia . '</b><br><br><br><br>
                            ________________________
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" align="center">
                            <b><br><br><br>' . $anNakhoda . ' Nakhoda KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_nahkoda . '</u></b><br>
                            <b>NIP. ' . $data->nip_nahkoda . '</b>
                        </td>
                        <td width="20%" align="center">
                            <b>Menyaksikan:</b>
                        </td>
                        <td width="40%" align="center">
                            <b><br><br><br>' . $anStaf . ' ' . $data->jabatan_staf_pangkalan . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_staf_pagkalan . '</u></b><br>
                            <b>NIP. ' . $data->nip_staf . '</b>
                        </td>
                    </tr>
                </table>';

            // Write HTML to PDF (sesuai template CodeIgniter)
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generate filename (sesuai template CodeIgniter)
            $filename = 'BA_Pemeriksaan_Sarana_Pengisian_' . str_replace('/', '_', $data->nomor_surat) . '_' . date('Y-m-d_H-i-s');

            // Output PDF (sesuai template CodeIgniter)
            $path = public_path('ba_pdf/' . $filename . '.pdf');
            $pdf->Output($path, 'F');

            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil dibuat',
                'filename' => $filename . '.pdf',
                'download_url' => asset('ba_pdf/' . $filename . '.pdf')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload dokumen pendukung
     */
    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Max 10MB
        ]);

        try {
            $ba = BbmKapaltrans::where('status_ba', 4)->findOrFail($id);

            if ($request->hasFile('document')) {
                $file = $request->file('document');

                // Generate unique filename
                $filename = 'ba_' . $ba->trans_id . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Move file to public/uploads/ba-documents
                $file->move(public_path('uploads/ba-documents'), $filename);

                // Update database
                $ba->update(['file_upload' => $filename]);

                return response()->json([
                    'success' => true,
                    'data' => $ba,
                    'message' => 'Dokumen berhasil diupload',
                    'filename' => $filename
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View dokumen pendukung
     */
    public function viewDocument($id)
    {
        try {
            $ba = BbmKapaltrans::where('status_ba', 4)->findOrFail($id);

            if (!$ba->file_upload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }

            $filePath = public_path('uploads/ba-documents/' . $ba->file_upload);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File dokumen tidak ditemukan di server'
                ], 404);
            }

            $fileUrl = asset('uploads/ba-documents/' . $ba->file_upload);

            return response()->json([
                'success' => true,
                'file_url' => $fileUrl,
                'filename' => $ba->file_upload
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuka dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete dokumen pendukung
     */
    public function deleteDocument($id)
    {
        try {
            $ba = BbmKapaltrans::where('status_ba', 4)->findOrFail($id);

            if ($ba->file_upload) {
                $filePath = public_path('uploads/ba-documents/' . $ba->file_upload);

                // Delete file from server
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Update database
                $ba->update(['file_upload' => '']);

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil dihapus'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
}
