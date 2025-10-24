<?php

namespace App\Http\Controllers\Web\monitoring;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BaAkhirBulanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kapals = MKapal::all();

        return view('ba-akhir-bulan.index', compact('kapals'));
    }

    /**
     * Get BA Akhir Bulan data via AJAX
     */
    public function getData(Request $request)
    {
        $query = BbmKapaltrans::with(['kapal'])
            ->where('status_ba', 1); // BA Akhir Bulan (sesuai project_ci)

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

            // Cek apakah sudah ada BA Akhir Bulan untuk tanggal dan kapal ini
            $existingBa = BbmKapaltrans::where('kapal_code', $kapal->code_kapal)
                ->where('tanggal_surat', $tanggalSurat)
                ->where('status_ba', 1)
                ->first();

            if ($existingBa) {
                return response()->json([
                    'success' => false,
                    'message' => 'BA Akhir Bulan untuk kapal dan tanggal ini sudah ada'
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
            'volume_sebelum' => 'required|numeric|min:0',
            'tanggal_sebelum' => 'required|date',
            'volume_pengisian' => 'required|numeric|min:0',
            'tanggal_pengisian' => 'nullable|date',
            'volume_sisa' => 'required|numeric|min:0',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pagkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:50',
            'nip_nahkoda' => 'nullable|string|max:20',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
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

            // Get the highest ID and add 1
            $maxId = BbmKapaltrans::max('trans_id') ?? 0;
            $newId = $maxId + 1;

            // Calculate volume usage (sesuai formula project_ci)
            // volume_pemakaian = (volume_sebelum + volume_pengisian) - volume_sisa
            $volumePemakaian = ($request->volume_sebelum + $request->volume_pengisian) - $request->volume_sisa;

            $ba = BbmKapaltrans::create([
                'trans_id' => $newId,
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'status_ba' => 1, // BA Akhir Bulan (sesuai project_ci)
                'volume_sisa' => $request->volume_sisa, // Volume sisa akhir bulan
                'volume_sebelum' => $request->volume_sebelum,
                'tanggal_sebelum' => $request->tanggal_sebelum,
                'volume_pengisian' => $request->volume_pengisian,
                'tanggal_pengisian' => $request->tanggal_pengisian,
                'volume_pemakaian' => $request->volume_pemakaian,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nakoda,
                'nip_nahkoda' => $request->nip_nakoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
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
                'message' => 'BA Akhir Bulan berhasil dibuat',
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
    public function show(BbmKapaltrans $baAkhirBulan)
    {
        return response()->json([
            'success' => true,
            'data' => $baAkhirBulan->load('kapal.upt')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the record first
        $baAkhirBulan = BbmKapaltrans::find($id);

        if (!$baAkhirBulan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        // Check if nomor_surat is being changed
        $nomorSuratRules = 'required|string|max:50';
        if ($request->nomor_surat !== $baAkhirBulan->nomor_surat) {
            $nomorSuratRules .= '|unique:bbm_kapaltrans,nomor_surat,' . $baAkhirBulan->trans_id . ',trans_id';
        }

        $validator = Validator::make($request->all(), [
            'kapal_id' => 'required|exists:m_kapal,m_kapal_id',
            'nomor_surat' => $nomorSuratRules,
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'volume_sebelum' => 'required|numeric|min:0',
            'tanggal_sebelum' => 'required|date',
            'volume_pengisian' => 'required|numeric|min:0',
            'tanggal_pengisian' => 'nullable|date',
            'volume_sisa' => 'required|numeric|min:0',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pagkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:50',
            'nip_nahkoda' => 'nullable|string|max:20',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
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

            // Calculate volume usage (sesuai formula project_ci)
            // volume_pemakaian = (volume_sebelum + volume_pengisian) - volume_sisa
            $volumePemakaian = ($request->volume_sebelum + $request->volume_pengisian) - $request->volume_sisa;

            $baAkhirBulan->update([
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'volume_sisa' => $request->volume_sisa,
                'volume_sebelum' => $request->volume_sebelum,
                'tanggal_sebelum' => $request->tanggal_sebelum,
                'volume_pengisian' => $request->volume_pengisian,
                'tanggal_pengisian' => $request->tanggal_pengisian,
                'volume_pemakaian' => $request->volume_pemakaian,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nakoda,
                'nip_nahkoda' => $request->nip_nakoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'link_modul_ba' => $request->link_ba,
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Akhir Bulan berhasil diperbarui',
                'data' => $baAkhirBulan->load('kapal')
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
    public function destroy(BbmKapaltrans $baAkhirBulan)
    {
        try {
            $baAkhirBulan->delete();

            return response()->json([
                'success' => true,
                'message' => 'BA Akhir Bulan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format tanggal Indonesia sesuai project_ci
     */
    private function indo_date($tgl)
    {
        $tgl_s = date('j', strtotime($tgl));
        $bln_s = $this->get_bulan(date('n', strtotime($tgl)));
        $thn_s = date('Y', strtotime($tgl));
        return $tgl_s . ' ' . $bln_s . ' ' . $thn_s;
    }

    /**
     * Get nama bulan Indonesia
     */
    private function get_bulan($bln)
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

    /**
     * Generate PDF for the specified resource.
     * Template telah disesuaikan 100% dengan template CodeIgniter di prooject_ci/application/models/dokumen/Dokumen_cetak.php
     * Function: cetak_ba_akhir_bulan() - Semua elemen template sudah sama persis termasuk:
     * - Header dengan logo KKP dan informasi UPT
     * - Judul "BERITA ACARA AKHIR BULAN"
     * - Format tanggal Indonesia (indo_date)
     * - Tabel volume dengan 4 baris (Sisa BBM Bulan Sebelumnya, Jumlah Pengisian, Jumlah Pemakaian, Volume Akhir Bulan)
     * - Footer dengan 3 tanda tangan (Nakhoda, KKM, Staf UPT)
     * - Format filename "BA_Akhir_Bulan_"
     */
    public function generatePdf($id)
    {
        try {
            // Find the record first
            $baAkhirBulan = BbmKapaltrans::find($id);

            if (!$baAkhirBulan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Load relationship data
            $baAkhirBulan->load(['kapal.upt']);

            // Get data
            $data = $baAkhirBulan;
            $kapal = $data->kapal;
            $upt = $kapal ? $kapal->upt : null;

            // Format date
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

            // Content
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td width="100%" align="center">
                            <font size="12"><b><u>BERITA ACARA AKHIR BULAN</b></u></font>
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
                        <td width="100%" align="justify">Menyatakan bahwa telah melakukan pengukuran sisa bbm akhir bulan dengan rincian sebagai berikut :
                        </td>
                    </tr>
                </table>';

            // Format dates sesuai project_ci (menggunakan indo_date format)
            $tanggalSebelumFormatted = $this->indo_date($data->tanggal_sebelum);

            // Volume table sesuai template project_ci - persis sama dengan cetak_ba_akhir_bulan()
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="1">
                    <tr>
                        <td width="73%" align="justify">&nbsp; Sisa BBM Bulan Sebelumnya, tanggal ' . $tanggalSebelumFormatted . '</td>
                        <td width="3%" align="center">=</td>
                        <td width="17%" align="right">' . number_format($data->volume_sebelum) . '&nbsp;&nbsp;&nbsp;</td>
                        <td width="auto" align="justify">&nbsp;Liter</td>
                    </tr>
                    <tr>
                        <td width="73%" align="justify">&nbsp; Jumlah Pengisian</td>
                        <td width="3%" align="center">=</td>
                        <td width="17%" align="right">' . number_format($data->volume_pengisian) . '&nbsp;&nbsp;&nbsp;</td>
                        <td width="auto" align="justify">&nbsp;Liter</td>
                    </tr>
                    <tr>
                        <td width="73%" align="justify">&nbsp; Jumlah Pemakaian</td>
                        <td width="3%" align="center">=</td>
                        <td width="17%" align="right">' . number_format($data->volume_pemakaian) . '&nbsp;&nbsp;&nbsp;</td>
                        <td width="auto" align="justify">&nbsp;Liter</td>
                    </tr>
                    <tr>
                        <td width="73%" align="justify">&nbsp; Volume Akhir Bulan</td>
                        <td width="3%" align="center">=</td>
                        <td width="17%" align="right">' . number_format($data->volume_sisa) . '&nbsp;&nbsp;&nbsp;</td>
                        <td width="auto" align="justify">&nbsp;Liter</td>
                    </tr>
                </table>';

            // Closing statement
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td width="100%" align="justify">Demikian Berita Acara Akhir Bulan ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.
                        </td>
                    </tr>
                </table>';

            // Footer signatures sesuai template project_ci
            $html .= '<br><br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border="0">
                    <tr>
                        <td width="40%" align="center">
                            <b>' . $anNakhoda . ' Nakhoda KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_nahkoda . '</u></b><br>
                            <b>NIP. ' . $data->nip_nahkoda . '</b>
                        </td>
                        <td width="20%" align="center"></td>
                        <td width="40%" align="center">
                            <b>' . $anKkm . ' KKM KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_kkm . '</u></b><br>
                            <b>NIP. ' . $data->nip_kkm . '</b>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" align="center"></td>
                        <td width="40%" align="center">
                            <b><br><br>Menyaksikan:</b><br>
                            <b>' . $anStaf . ' ' . $data->jabatan_staf_pangkalan . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_staf_pagkalan . '</u></b><br>
                            <b>NIP. ' . $data->nip_staf . '</b>
                        </td>
                        <td width="30%" align="center"></td>
                    </tr>
                </table>';

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generate filename (sesuai template project_ci)
            $filename = 'BA_Akhir_Bulan_' . str_replace('/', '_', $data->nomor_surat) . '_' . date('Y-m-d_H-i-s');

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
            $ba = BbmKapaltrans::where('status_ba', 1)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 1)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 1)->findOrFail($id);

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
