<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\BbmTransdetail;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use TCPDF;

class BaPenerimaanHibahBbmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kapals = MKapal::all();
        $upts = MUpt::all();

        return view('ba-penerimaan-hibah-bbm.index', compact('kapals', 'upts'));
    }

    /**
     * Get BA Penerimaan Hibah BBM data via AJAX
     */
    public function getData(Request $request)
    {
        $query = BbmKapaltrans::with(['kapal'])
            ->where('status_ba', 17); // BA Penerimaan Hibah BBM

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
                'nama_nakoda' => $kapal->nama_nakoda,
                'nip_nakoda' => $kapal->nip_nakoda,
                'nama_kkm' => $kapal->nama_kkm,
                'nip_kkm' => $kapal->nip_kkm,
            ]
        ]);
    }

    /**
     * Get UPT data for instansi pemberi
     */
    public function getUptData(Request $request)
    {
        $code = $request->code_upt;

        if ($code == '999') {
            return response()->json([
                'success' => true,
                'data' => [
                    'nama' => 'INSTANSI LAINNYA',
                    'alamat1' => '',
                ]
            ]);
        }

        $upt = MUpt::where('code', $code)->first();

        if (!$upt) {
            return response()->json([
                'success' => false,
                'message' => 'UPT tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nama' => $upt->nama,
                'alamat1' => $upt->alamat1,
            ]
        ]);
    }

    /**
     * Get BA data berdasarkan tanggal dan kapal untuk auto-fill volume_sebelum
     */
    public function getBaData(Request $request)
    {
        $tanggalSurat = $request->tanggal_surat;
        $kapalId = $request->m_kapal_id;

        // Cari BA dengan status_ba = 3 (BA Penggunaan BBM) di tanggal yang sama
        $ba = BbmKapaltrans::join('m_kapal', 'm_kapal.code_kapal', '=', 'bbm_kapaltrans.kapal_code')
            ->where('bbm_kapaltrans.tanggal_surat', $tanggalSurat)
            ->where('bbm_kapaltrans.status_ba', 3)
            ->where('m_kapal.m_kapal_id', $kapalId)
            ->orderBy('bbm_kapaltrans.tanggal_surat', 'desc')
            ->orderBy('bbm_kapaltrans.jam_surat', 'desc')
            ->select('bbm_kapaltrans.*')
            ->first();

        if (!$ba) {
            return response()->json([
                'success' => false,
                'jml' => 0,
                'message' => 'Hari ini anda belum melakukan sounding/pengukuran',
                'nomor_surat' => '',
                'volume_sisa' => 0
            ]);
        }

        return response()->json([
            'success' => true,
            'jml' => 1,
            'nomor_surat' => $ba->nomor_surat,
            'volume_sisa' => $ba->volume_sisa ?? 0,
        ]);
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
            'jam_surat' => 'required',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'link_ba' => 'nullable|string|max:50',
            'code_upt' => 'required|string',
            'instansi_temp' => 'required|string|max:100',
            'alamat_instansi_temp' => 'nullable|string',
            'penyedia' => 'nullable|string|max:100',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'volume_sebelum' => 'required|numeric',
            'no_so' => 'nullable|string|max:50',
            'jabatan_staf_pangkalan' => 'nullable|string|max:50',
            'nama_staf_pangkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_penyedia' => 'nullable|string|max:30',
            'nama_nahkoda' => 'nullable|string|max:30',
            'nip_nahkoda' => 'nullable|string|max:20',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
            'transportasi' => 'required|array',
            'transportasi.*' => 'nullable|string',
            'no_do' => 'required|array',
            'no_do.*' => 'nullable|string',
            'volume_isi' => 'required|array',
            'volume_isi.*' => 'nullable|numeric',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $kapal = MKapal::find($request->kapal_id);

            // Get max trans_id and increment
            $maxTransId = BbmKapaltrans::max('trans_id') ?? 0;
            $newTransId = $maxTransId + 1;

            // Insert into bbm_kapaltrans
            $ba = BbmKapaltrans::create([
                'trans_id' => $newTransId,
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                // 'instansi_temp' => $request->code_upt,
                'status_ba' => 17,
                'volume_sebelum' => $request->volume_sebelum,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pangkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'no_so' => $request->no_so,
                'instansi_temp' => $request->code_upt,
                'alamat_instansi_temp' => $request->alamat_instansi_temp,
                'nama_penyedia' => $request->nama_penyedia,
                'link_modul_ba' => $request->link_ba,
                'penyedia' => $request->penyedia,
            ]);

            // Insert into bbm_transdetail
            $totalVolume = 0;
            for ($i = 0; $i < count($request->transportasi); $i++) {
                if (!empty($request->transportasi[$i]) || !empty($request->no_do[$i]) || !empty($request->volume_isi[$i])) {
                    $maxTransDetailId = BbmTransdetail::max('bbm_transdetail_id') ?? 0;
                    $newTransDetailId = $maxTransDetailId + 1;
                    BbmTransdetail::create([
                        'bbm_transdetail_id' => $newTransDetailId,
                        'nomor_surat' => $request->nomor_surat,
                        'transportasi' => $request->transportasi[$i] ?? '',
                        'no_so' => $request->no_so,
                        'no_do' => $request->no_do[$i] ?? '',
                        'volume_isi' => $request->volume_isi[$i] ?? 0,
                        'keterangan' => $request->keterangan[$i] ?? '',
                        'tanggalinput' => now(),
                    ]);
                    $totalVolume += (float)($request->volume_isi[$i] ?? 0);
                }
            }

            // Update volume_pengisian and volume_sisa
            $volumeSisa = $request->volume_sebelum + $totalVolume;
            $ba->update([
                'volume_pengisian' => $totalVolume,
                'volume_sisa' => $volumeSisa,
            ]);

            // Update link_modul_ba di BA sebelumnya
            if ($request->link_ba) {
                BbmKapaltrans::where('nomor_surat', $request->link_ba)->update([
                    'link_modul_ba' => $request->nomor_surat,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA berhasil dibuat',
                'data' => $ba
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ba = BbmKapaltrans::with(['kapal', 'transdetails'])
            ->where('status_ba', 17)
            ->where('trans_id', $id)
            ->first();

        if (!$ba) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Debug log
        \Log::info('BA Data for ID ' . $id, [
            'instansi_temp' => $ba->instansi_temp,
            'alamat_instansi_temp' => $ba->alamat_instansi_temp,
            'nama_staf_pagkalan' => $ba->nama_staf_pagkalan,
            'nip_staf' => $ba->nip_staf
        ]);

        return response()->json([
            'success' => true,
            'data' => $ba
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ba = BbmKapaltrans::where('status_ba', 17)
            ->where('trans_id', $id)
            ->first();

        if (!$ba) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'code_upt' => 'required|string',
            'instansi_temp' => 'required|string|max:100',
            'alamat_instansi_temp' => 'nullable|string',
            'penyedia' => 'nullable|string|max:100',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'no_so' => 'nullable|string|max:50',
            'jabatan_staf_pangkalan' => 'nullable|string|max:50',
            'nama_staf_pangkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_penyedia' => 'nullable|string|max:30',
            'nama_nahkoda' => 'nullable|string|max:30',
            'nip_nahkoda' => 'nullable|string|max:20',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
            'transportasi' => 'required|array',
            'transportasi.*' => 'nullable|string',
            'no_do' => 'required|array',
            'no_do.*' => 'nullable|string',
            'volume_isi' => 'required|array',
            'volume_isi.*' => 'nullable|numeric',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update bbm_kapaltrans
            $ba->update([
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pangkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                // 'instansi_temp' => $request->code_upt,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'no_so' => $request->no_so,
                'instansi_temp' => $request->code_upt,
                'alamat_instansi_temp' => $request->alamat_instansi_temp,
                'nama_penyedia' => $request->nama_penyedia,
                'penyedia' => $request->penyedia,
            ]);

            // Delete existing transdetails
            BbmTransdetail::where('nomor_surat', $ba->nomor_surat)->delete();

            // Re-insert transdetails
            $totalVolume = 0;
            for ($i = 0; $i < count($request->transportasi); $i++) {
                if (!empty($request->transportasi[$i]) || !empty($request->no_do[$i]) || !empty($request->volume_isi[$i])) {
                    $maxTransDetailId = BbmTransdetail::max('bbm_transdetail_id') ?? 0;
                    $newTransDetailId = $maxTransDetailId + 1;
                    BbmTransdetail::create([
                        'bbm_transdetail_id' => $newTransDetailId,
                        'nomor_surat' => $ba->nomor_surat,
                        'transportasi' => $request->transportasi[$i] ?? '',
                        'no_so' => $request->no_so,
                        'no_do' => $request->no_do[$i] ?? '',
                        'volume_isi' => $request->volume_isi[$i] ?? 0,
                        'keterangan' => $request->keterangan[$i] ?? '',
                        'tanggalinput' => now(),
                    ]);
                    $totalVolume += (float)($request->volume_isi[$i] ?? 0);
                }
            }

            // Update volume_pengisian only (volume_sebelum and volume_sisa stay the same from original)
            $volumeSisa = $ba->volume_sebelum + $totalVolume;
            $ba->update([
                'volume_pengisian' => $totalVolume,
                'volume_sisa' => $volumeSisa,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA berhasil diupdate',
                'data' => $ba
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal update data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $ba = BbmKapaltrans::where('status_ba', 17)
                ->where('trans_id', $id)
                ->first();

            if (!$ba) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Delete transdetails first
            BbmTransdetail::where('nomor_surat', $ba->nomor_surat)->delete();

            // Delete BA
            $ba->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF for BA Penerimaan Hibah BBM
     */
    public function generatePdf($id)
    {
        try {
            $data = BbmKapaltrans::with(['kapal.upt', 'transdetails'])
                ->where('status_ba', 17)
                ->where('trans_id', $id)
                ->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $kapal = $data->kapal;

            // Create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('SIGOTIK');
            $pdf->SetTitle('BA PENERIMAAN HIBAH BBM');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(15, 15, 15);

            // Add a page
            $pdf->AddPage();

            // Set font
            $pdf->SetFont('times', '', 11);

            // Determine An. checkboxes
            $anStaf = ($data->an_staf == 1) ? 'a.n. ' : '';
            $anNakhoda = ($data->an_nakhoda == 1) ? 'a.n. ' : '';
            $anKkm = ($data->an_kkm == 1) ? 'a.n. ' : '';

        // Get UPT data
        $upt = $kapal->upt ?? null;
        
        // Header content with KKP letterhead
        $html = '
        <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border="0">
            <tr>
                <td width="15%" align="center">
                    <img align="center" width="120" height="120" src="' . public_path('images/logo-kkp.png') . '" border="0" />
                </td>
                <td width="85%" align="center">
                    <font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
                    <font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
                    <font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
                    <font size="12"><b><i>' . strtoupper($upt->nama ?? 'UPT') . '</b></i></font><br>
                    <font size="10">' . ($upt->alamat1 ?? '') . '</font><br>
                    <font size="10">' . ($upt->alamat2 ?? '') . '</font><br>
                    <font size="10">' . ($upt->alamat3 ?? '') . '</font>
                </td>
            </tr>
        </table>
        <br><br>
        
        <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
            <tr>
                <td width="100%" align="center">
                    <font size="12"><b><u>BERITA ACARA PENERIMAAN HIBAH BBM</b></u></font>
                </td>
            </tr>
            <tr>
                <td width="100%" align="center">
                    <b>Nomor : ' . htmlspecialchars($data->nomor_surat) . '</b><br>
                </td>
            </tr>
        </table>
        <br>

        <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
            <tr>
                <td width="100%" align="justify">Pada hari ini ' . $this->hari_indo($data->tanggal_surat) . ' pukul ' . $data->jam_surat . ' ' . $data->zona_waktu_surat . ' bertempat di ' . htmlspecialchars($data->lokasi_surat) . ', kami yang bertanda tangan di bawah ini :</td>
            </tr>
            <tr>
                <td width="100%" align="justify"></td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
            <tr>
                <td width="20%" align="justify">Nama/Jabatan</td>
                <td width="1%" align="justify">:</td>
                <td width="auto" align="justify">1.' . htmlspecialchars($data->nama_nahkoda) . ' / Nakhoda Kapal Pengawas</td>
            </tr>
            <tr>
                <td align="justify"></td>
                <td align="justify"></td>
                <td align="justify">2.' . htmlspecialchars($data->nama_kkm) . ' / KKM Kapal Pengawas</td>
            </tr>
            <tr>
                <td align="justify"></td>
                <td align="justify"></td>
                <td align="justify"></td>
            </tr>
            <tr>
                <td align="justify">Alamat</td>
                <td align="justify">:</td>
                <td align="justify">' . htmlspecialchars($data->alamat_instansi_temp) . '</td>
            </tr>
            <tr>
                <td align="justify"></td>
                <td align="justify"></td>
                <td align="justify"></td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
            <tr>
                <td width="100%" align="justify">Menyatakan bahwa telah menerima hasil pengadaan Hibah BBM ' . htmlspecialchars($data->keterangan_jenis_bbm) . ' dari ' . htmlspecialchars($data->instansi_temp) . ' melalui penyedia ' . htmlspecialchars($data->penyedia ?? '-') . ' (transportir/pengirim) sebagai berikut :</td>
            </tr>
            <tr>
                <td width="100%" align="justify"></td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="1">
            <tr>
                <td width="5%" align="center"><b>No</b></td>
                <td align="center"><b>Transportasi</b></td>
                <td align="center"><b>Nomor SO</b></td>
                <td align="center"><b>Nomor DO</b></td>
                <td align="center"><b>Volume (Liter)</b></td>
                <td width="auto" align="center"><b>Keterangan</b></td>
            </tr>';

        $no = 1;
        $totalVolume = 0;
        foreach ($data->transdetails as $detail) {
            $html .= '
            <tr>
                <td width="5%" align="center">' . $no++ . '</td>
                <td align="center">' . htmlspecialchars($detail->transportasi) . '</td>
                <td align="center">' . htmlspecialchars($data->no_so ?? '-') . '</td>
                <td align="center">' . htmlspecialchars($detail->no_do) . '</td>
                <td align="center">' . number_format($detail->volume_isi, 0, ',', '.') . ' Liter</td>
                <td width="auto" align="center">' . htmlspecialchars($detail->keterangan) . '</td>
            </tr>';
            $totalVolume += $detail->volume_isi;
        }

        $html .= '
            <tr>
                <td colspan="4" align="center">JUMLAH</td>
                <td align="center">' . number_format($totalVolume, 0, ',', '.') . ' Liter</td>
                <td width="auto" align="center"></td>
            </tr>
        </table>
        <br>

        <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
            <tr>
                <td width="100%" align="justify"></td>
            </tr>
            <tr>
                <td width="100%" align="justify">Demikian Berita Acara Penerimaan BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
            </tr>
        </table>
        <br><br>

        <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border="0">
            <tr>
                <td width="40%" align="center">
                    <b>' . $anKkm . 'KKM KP ' . htmlspecialchars($kapal->nama_kapal ?? '-') . '</b><br><br><br><br>
                </td>
                <td width="20%" align="center"></td>
                <td width="40%" align="center">
                    <b>Penyedia BBM/Pengirim</b><br>
                    <b>' . htmlspecialchars($data->penyedia ?? '-') . '</b>
                </td>
            </tr>
            <tr>
                <td width="40%" align="center">
                    <b><u>' . htmlspecialchars($data->nama_kkm) . '</u></b><br>
                    <b><u>' . htmlspecialchars($data->nip_kkm) . '</u></b><br>
                </td>
                <td width="20%" align="center">
                    <b></b><br>
                    <b></b><br>
                    <b>Menyaksikan:</b>
                </td>
                <td width="40%" align="center">
                    <b><u>' . htmlspecialchars($data->nama_penyedia ?? '-') . '</u></b><br>
                    <b></b>
                </td>
            </tr>
            <tr>
                <td width="40%" align="center">
                    <b>' . $anNakhoda . 'Nakhoda KP. ' . htmlspecialchars($kapal->nama_kapal ?? '-') . '</b><br><br><br><br>
                    <b><u>' . htmlspecialchars($data->nama_nahkoda) . '</u></b><br>
                    <b>NIP. ' . htmlspecialchars($data->nip_nahkoda) . '</b><br>
                </td>
                <td width="20%" align="center"></td>
                <td width="40%" align="center">
                    <b>' . $anStaf . ' ' . htmlspecialchars($data->jabatan_staf_pangkalan) . '</b><br><br><br><br><br>
                    <b><u>' . htmlspecialchars($data->nama_staf_pagkalan) . '</u></b><br>
                    <b>NIP. ' . htmlspecialchars($data->nip_staf) . '</b>
                </td>
            </tr>
        </table>
        ';

        // Add separator lines like in CI
        $style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        $pdf->Line(10, 58, 200, 58, $style);
        $style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        $pdf->Line(10, 60, 200, 60, $style2);

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

            // Close and output PDF document
            $pdfContent = $pdf->Output('BA_Penerimaan_Hibah_BBM_' . $data->nomor_surat . '.pdf', 'S');

            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="BA_Penerimaan_Hibah_BBM_' . $data->nomor_surat . '.pdf"');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload supporting document
     */
    public function uploadDocument(Request $request, $id)
    {
        $ba = BbmKapaltrans::where('status_ba', 17)
            ->where('trans_id', $id)
            ->first();

        if (!$ba) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->hasFile('document')) {
                // Delete old file if exists
                if ($ba->file_upload && file_exists(public_path('uploads/ba-documents/' . $ba->file_upload))) {
                    unlink(public_path('uploads/ba-documents/' . $ba->file_upload));
                }

                $file = $request->file('document');
                $filename = 'ba_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/ba-documents'), $filename);

                $ba->update([
                    'file_upload' => $filename,
                    'status_upload' => 1
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil diupload',
                    'filename' => $filename
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View uploaded document
     */
    public function viewDocument($id)
    {
        $ba = BbmKapaltrans::where('status_ba', 17)
            ->where('trans_id', $id)
            ->first();

        if (!$ba || !$ba->file_upload) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'file_url' => asset('uploads/ba-documents/' . $ba->file_upload)
        ]);
    }

    /**
     * Delete uploaded document
     */
    public function deleteDocument($id)
    {
        try {
            $ba = BbmKapaltrans::where('status_ba', 17)
                ->where('trans_id', $id)
                ->first();

            if (!$ba) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            if ($ba->file_upload && file_exists(public_path('uploads/ba-documents/' . $ba->file_upload))) {
                unlink(public_path('uploads/ba-documents/' . $ba->file_upload));
            }

            $ba->update([
                'file_upload' => null,
                'status_upload' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper functions
    private function hari_indo($tanggal)
    {
        $hari = date('D', strtotime($tanggal));
        $hariIndo = [
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu'
        ];
        return $hariIndo[$hari];
    }

    private function indo_date($tgl)
    {
        $tanggal = substr($tgl, 8, 2);
        $bulan = $this->get_bulan(substr($tgl, 5, 2));
        $tahun = substr($tgl, 0, 4);
        return $tanggal . ' ' . $bulan . ' ' . $tahun;
    }

    private function get_bulan($bln)
    {
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        return $bulan[$bln];
    }
}
