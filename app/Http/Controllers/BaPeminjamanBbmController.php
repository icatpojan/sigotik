<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BaPeminjamanBbmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kapals = MKapal::all();
        $persetujuans = \App\Models\MPersetujuan::all();

        return view('ba-peminjaman-bbm.index', compact('kapals', 'persetujuans'));
    }

    /**
     * Get BA Peminjaman BBM data via AJAX
     */
    public function getData(Request $request)
    {
        $query = BbmKapaltrans::with(['kapal'])
            ->where('status_ba', 10); // BA Peminjaman BBM

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

            // Cek apakah sudah ada BA Peminjaman BBM untuk tanggal dan kapal ini
            $existingBa = BbmKapaltrans::where('kapal_code', $kapal->code_kapal)
                ->where('tanggal_surat', $tanggalSurat)
                ->where('status_ba', 10)
                ->first();

            if ($existingBa) {
                return response()->json([
                    'success' => false,
                    'message' => 'BA Peminjaman BBM untuk kapal dan tanggal ini sudah ada'
                ], 400);
            }

            // Cari BA sebelumnya (status_ba = 2,3) yang belum di-link untuk BA Peminjaman BBM
            $baSebelumnya = BbmKapaltrans::where('kapal_code', $kapal->code_kapal)
                ->where('tanggal_surat', $tanggalSurat)
                ->whereIn('status_ba', [2, 3]) // BA Sounding dan BA Penggunaan
                ->where('link_modul_ba', null)
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
            'kapal_peminjam_id' => 'required|exists:m_kapal,m_kapal_id',
            'volume_sebelum' => 'required|numeric|min:0',
            'volume_pemakaian' => 'required|numeric|min:0',
            'volume_sisa' => 'required|numeric|min:0',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'sebab_temp' => 'required|string|max:255',
            'nomer_persetujuan' => 'required|string|max:50',
            'tgl_persetujuan' => 'required|date',
            'm_persetujuan_id' => 'required|exists:m_persetujuan,id',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pagkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:30',
            'nip_nahkoda' => 'nullable|string|max:20',
            'pangkat_nahkoda' => 'nullable|string|max:30',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'nama_nahkoda_temp' => 'nullable|string|max:30',
            'nip_nahkoda_temp' => 'nullable|string|max:20',
            'pangkat_nahkoda_temp' => 'nullable|string|max:30',
            'nama_kkm_temp' => 'nullable|string|max:30',
            'nip_kkm_temp' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
            'an_nakhoda_temp' => 'nullable|in:0,1',
            'an_kkm_temp' => 'nullable|in:0,1',
            'link_ba' => 'nullable|string|max:50',
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

            // Get kapal data (kapal pemberi)
            $kapal = MKapal::find($request->kapal_id);
            $kapalPeminjam = MKapal::find($request->kapal_peminjam_id);

            // Get the highest ID and add 1
            $maxId = BbmKapaltrans::max('trans_id') ?? 0;
            $newId = $maxId + 1;

            // Validasi volume peminjaman tidak boleh melebihi volume tersedia
            if ($request->volume_pemakaian > $request->volume_sebelum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Volume peminjaman tidak boleh melebihi volume tersedia'
                ], 400);
            }

            $ba = BbmKapaltrans::create([
                'trans_id' => $newId,
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'status_ba' => 10, // BA Peminjaman BBM
                'volume_sebelum' => $request->volume_sebelum,
                'volume_pemakaian' => $request->volume_pemakaian,
                'volume_sisa' => $request->volume_sisa,
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'sebab_temp' => $request->sebab_temp,
                'nomer_persetujuan' => $request->nomer_persetujuan,
                'tgl_persetujuan' => $request->tgl_persetujuan,
                'm_persetujuan_id' => $request->m_persetujuan_id,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'pangkat_nahkoda' => $request->pangkat_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'kapal_code_temp' => $kapalPeminjam->code_kapal,
                'nama_nahkoda_temp' => $request->nama_nahkoda_temp,
                'nip_nahkoda_temp' => $request->nip_nahkoda_temp,
                'pangkat_nahkoda_temp' => $request->pangkat_nahkoda_temp,
                'nama_kkm_temp' => $request->nama_kkm_temp,
                'nip_kkm_temp' => $request->nip_kkm_temp,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'an_nakhoda_temp' => (int)($request->an_nakhoda_temp == '1'),
                'an_kkm_temp' => (int)($request->an_kkm_temp == '1'),
                'status_temp' => 0,
                'link_modul_ba' => $request->link_ba,
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'status_trans' => 0, // Input
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update link BA sebelumnya jika ada
            if ($request->link_ba) {
                BbmKapaltrans::where('nomor_surat', $request->link_ba)
                    ->update(['link_modul_ba' => $request->nomor_surat]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Peminjaman BBM berhasil dibuat',
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
    public function show(BbmKapaltrans $baPeminjamanBbm)
    {
        // Get kapal peminjam data based on kapal_code_temp
        $kapalPeminjam = null;
        if ($baPeminjamanBbm->kapal_code_temp) {
            $kapalPeminjam = MKapal::where('code_kapal', $baPeminjamanBbm->kapal_code_temp)->first();
        }

        $data = $baPeminjamanBbm->load('kapal.upt');

        // Add kapal peminjam ID to the data
        if ($kapalPeminjam) {
            $data->kapal_peminjam_id = $kapalPeminjam->m_kapal_id;
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BbmKapaltrans $baPeminjamanBbm)
    {
        $validator = Validator::make($request->all(), [
            'kapal_id' => 'required|exists:m_kapal,m_kapal_id',
            'nomor_surat' => 'required|string|max:50|unique:bbm_kapaltrans,nomor_surat,' . $baPeminjamanBbm->trans_id . ',trans_id',
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'kapal_peminjam_id' => 'required|exists:m_kapal,m_kapal_id',
            'volume_sebelum' => 'required|numeric|min:0',
            'volume_pemakaian' => 'required|numeric|min:0',
            'volume_sisa' => 'required|numeric|min:0',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'sebab_temp' => 'required|string|max:255',
            'nomer_persetujuan' => 'required|string|max:50',
            'tgl_persetujuan' => 'required|date',
            'm_persetujuan_id' => 'required|exists:m_persetujuan,id',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pagkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:30',
            'nip_nahkoda' => 'nullable|string|max:20',
            'pangkat_nahkoda' => 'nullable|string|max:30',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'nama_nahkoda_temp' => 'nullable|string|max:30',
            'nip_nahkoda_temp' => 'nullable|string|max:20',
            'pangkat_nahkoda_temp' => 'nullable|string|max:30',
            'nama_kkm_temp' => 'nullable|string|max:30',
            'nip_kkm_temp' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
            'an_nakhoda_temp' => 'nullable|in:0,1',
            'an_kkm_temp' => 'nullable|in:0,1',
            'link_ba' => 'nullable|string|max:50',
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
            $kapalPeminjam = MKapal::find($request->kapal_peminjam_id);

            // Validasi volume peminjaman tidak boleh melebihi volume tersedia
            if ($request->volume_pemakaian > $request->volume_sebelum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Volume peminjaman tidak boleh melebihi volume tersedia'
                ], 400);
            }

            $baPeminjamanBbm->update([
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'volume_sebelum' => $request->volume_sebelum,
                'volume_pemakaian' => $request->volume_pemakaian,
                'volume_sisa' => $request->volume_sisa,
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'sebab_temp' => $request->sebab_temp,
                'nomer_persetujuan' => $request->nomer_persetujuan,
                'tgl_persetujuan' => $request->tgl_persetujuan,
                'm_persetujuan_id' => $request->m_persetujuan_id,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'pangkat_nahkoda' => $request->pangkat_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'kapal_code_temp' => $kapalPeminjam->code_kapal,
                'nama_nahkoda_temp' => $request->nama_nahkoda_temp,
                'nip_nahkoda_temp' => $request->nip_nahkoda_temp,
                'pangkat_nahkoda_temp' => $request->pangkat_nahkoda_temp,
                'nama_kkm_temp' => $request->nama_kkm_temp,
                'nip_kkm_temp' => $request->nip_kkm_temp,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'an_nakhoda_temp' => (int)($request->an_nakhoda_temp == '1'),
                'an_kkm_temp' => (int)($request->an_kkm_temp == '1'),
                'link_modul_ba' => $request->link_ba,
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Peminjaman BBM berhasil diperbarui',
                'data' => $baPeminjamanBbm->load('kapal')
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
    public function destroy(BbmKapaltrans $baPeminjamanBbm)
    {
        try {
            $baPeminjamanBbm->delete();

            return response()->json([
                'success' => true,
                'message' => 'BA Peminjaman BBM berhasil dihapus'
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
     */
    public function generatePdf(BbmKapaltrans $baPeminjamanBbm)
    {
        try {
            // Load relationship data
            $baPeminjamanBbm->load(['kapal.upt']);

            // Get data
            $data = $baPeminjamanBbm;
            $kapal = $data->kapal;
            $upt = $kapal ? $kapal->upt : null;

            // Format date
            $tanggalFormatted = $this->indoDate($data->tanggal_surat);
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

            // Build HTML content
            $html = '<style type="text/css">
                hr.new5 {
                    border: 20px solid green;
                    border-radius: 5px;
                }
            </style>';

            // Header
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

            // Get kapal peminjam data
            $kapalPeminjam = MKapal::where('code_kapal', $data->kapal_code_temp)->first();

            // Content
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td width="100%" align="center">
                            <font size="12"><b><u>BERITA ACARA PEMINJAMAN BBM</b></u></font>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" align="center">
                            <b>Nomor : ' . $data->nomor_surat . '</b><br>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" align="justify">Yang bertanda tangan di bawah ini :</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify">1.</td>
                        <td width="20%" align="justify">Nama</td>
                        <td width="3%" align="justify">:</td>
                        <td width="50%" align="justify">' . $data->nama_nahkoda_temp . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="20%" align="justify">Pangkat/Gol</td>
                        <td width="3%" align="justify">:</td>
                        <td width="50%" align="justify">' . $data->pangkat_nahkoda_temp . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="20%" align="justify">Jabatan</td>
                        <td width="3%" align="justify">:</td>
                        <td width="50%" align="justify">' . ($data->an_nakhoda_temp ? 'An. ' : '') . 'Nakhoda KP ' . ($kapalPeminjam ? $kapalPeminjam->nama_kapal : '') . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="80%" align="justify">Selanjutnya disebut sebagai <b>Pihak I selaku peminjam</b></td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify">2.</td>
                        <td width="20%" align="justify">Nama</td>
                        <td width="3%" align="justify">:</td>
                        <td width="50%" align="justify">' . $data->nama_nahkoda . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="20%" align="justify">Pangkat/Gol</td>
                        <td width="3%" align="justify">:</td>
                        <td width="30%" align="justify">' . $data->pangkat_nahkoda . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="20%" align="justify">Jabatan</td>
                        <td width="3%" align="justify">:</td>
                        <td width="50%" align="justify">' . ($data->an_nakhoda ? 'An. ' : '') . 'Nakhoda KP ' . ($kapal ? $kapal->nama_kapal : '') . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="80%" align="justify">Selanjutnya disebut sebagai <b>Pihak II selaku pemberi pinjaman</b></td>
                    </tr>
                </table>';

            // Get persetujuan data
            $persetujuan = \App\Models\MPersetujuan::find($data->m_persetujuan_id);
            $tanggalPersetujuanFormatted = $this->indoDate($data->tgl_persetujuan);

            // Narasi peminjaman
            $html .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                <tr>
                    <td></td>
                    </tr>
                    <tr>
                    <td width="100%" align="justify">Pada hari ini ' . $tanggalFormatted . ' pukul ' . $jamFormatted . ' ' . $data->zona_waktu_surat . ' bertempat di ' . $data->lokasi_surat . ' berdasarkan Surat Persetujuan dari ' . ($persetujuan ? $persetujuan->deskripsi_persetujuan : '') . '
                        Nomor ' . $data->nomer_persetujuan . ' tanggal ' . $tanggalPersetujuanFormatted . ', telah dilakukan peminjaman BBM ' . $data->keterangan_jenis_bbm . ' dari PIHAK II ke PIHAK I sebanyak <b>' . number_format($data->volume_pemakaian) . '</b> liter. Adapun peminjaman BBM ini di karenakan <b>' . $data->sebab_temp . '</b>
                    </td>
                    </tr>
                </table>';

            // Closing statement
            $html .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100%" align="justify">Demikian Berita Acara Peminjaman BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
                    </tr>
                </table>';

            // Footer signatures
            $html .= '<br><br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border="0">
                    <tr>
                        <td width="40%" align="center">
                            <b>Pihak I</b>
                        </td>
                        <td width="20%" align="center"></td>
                        <td width="40%" align="center">
                            <b>PIHAK II</b>
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" align="center">
                            <b>' . ($data->an_nakhoda_temp ? 'An. ' : '') . 'Nakhoda KP. ' . ($kapalPeminjam ? $kapalPeminjam->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_nahkoda_temp . '</u></b><br>
                            <b>NIP. ' . $data->nip_nahkoda_temp . '</b><br>
                        </td>
                        <td width="20%" align="center"></td>
                        <td width="40%" align="center">
                            <b>' . ($data->an_nakhoda ? 'An. ' : '') . 'Nakhoda KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_nahkoda . '</u></b><br>
                            <b>NIP. ' . $data->nip_nahkoda . '</b>
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" align="center">
                            <b>' . ($data->an_kkm_temp ? 'An. ' : '') . 'KKM KP. ' . ($kapalPeminjam ? $kapalPeminjam->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_kkm_temp . '</u></b><br>
                            <b>NIP. ' . $data->nip_kkm_temp . '</b><br>
                        </td>
                        <td width="20%" align="center">
                            <b><br><br>Menyaksikan:</b><br>
                            <b>' . $anStaf . ' ' . $data->jabatan_staf_pangkalan . '</b><br><br><br><br><br>
                        </td>
                        <td width="40%" align="center">
                            <b>' . ($data->an_kkm ? 'An. ' : '') . 'KKM KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_kkm . '</u></b><br>
                            <b>NIP. ' . $data->nip_kkm . '</b>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" align="center"></td>
                        <td width="40%" align="center">
                            <b><u>' . $data->nama_staf_pagkalan . '</u></b><br>
                            <b>NIP. ' . $data->nip_staf . '</b>
                        </td>
                        <td width="30%" align="center"></td>
                    </tr>
                </table>';

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generate filename
            $filename = 'BA_Peminjaman_BBM_' . str_replace('/', '_', $data->nomor_surat) . '_' . date('Y-m-d_H-i-s');

            // Output PDF
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
            $ba = BbmKapaltrans::where('status_ba', 10)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 10)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 10)->findOrFail($id);

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

    private function indoDate($tgl)
    {
        if (!$tgl || $tgl == '1970-01-01' || $tgl == '0000-00-00') {
            return '';
        }

        $tgl_s = date('j', strtotime($tgl));
        $bln_s = $this->getBulan(date('n', strtotime($tgl)));
        $thn_s = date('Y', strtotime($tgl));

        return $tgl_s . ' ' . $bln_s . ' ' . $thn_s;
    }

    private function getBulan($bln)
    {
        switch ($bln) {
            case '1':
                $nama_bln = 'Januari';
                break;
            case '2':
                $nama_bln = 'Februari';
                break;
            case '3':
                $nama_bln = 'Maret';
                break;
            case '4':
                $nama_bln = 'April';
                break;
            case '5':
                $nama_bln = 'Mei';
                break;
            case '6':
                $nama_bln = 'Juni';
                break;
            case '7':
                $nama_bln = 'Juli';
                break;
            case '8':
                $nama_bln = 'Agustus';
                break;
            case '9':
                $nama_bln = 'September';
                break;
            case '10':
                $nama_bln = 'Oktober';
                break;
            case '11':
                $nama_bln = 'November';
                break;
            case '12':
                $nama_bln = 'Desember';
                break;
        }
        return $nama_bln;
    }
}
