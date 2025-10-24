<?php

namespace App\Http\Controllers\Web\monitoring;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\BbmTransdetail;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BaPenerimaanBbmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kapals = MKapal::all();

        return view('ba-penerimaan-bbm.index', compact('kapals'));
    }

    /**
     * Get BA Penerimaan Bbm data via AJAX
     */
    public function getData(Request $request)
    {
        $query = BbmKapaltrans::with(['kapal', 'fotoUploads', 'transdetails'])
            ->where('status_ba', 5); // BA Penerimaan BBM (sesuai project_ci)

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

        // Recalculate volume_pengisian for each item if needed
        foreach ($data->items() as $item) {
            if ($item->transdetails && $item->transdetails->count() > 0) {
                $calculatedVolume = $item->transdetails->sum('volume_isi');
                if ($item->volume_pengisian != $calculatedVolume) {
                    // Update volume_pengisian if it doesn't match the sum of transdetails
                    $item->update(['volume_pengisian' => $calculatedVolume]);
                    $item->volume_pengisian = $calculatedVolume; // Update the current object
                }
            }
        }

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
     * Fix volume_pengisian for existing records
     */
    public function fixVolumePengisian()
    {
        try {
            $baRecords = BbmKapaltrans::where('status_ba', 5)
                ->with('transdetails')
                ->get();

            $fixedCount = 0;
            foreach ($baRecords as $ba) {
                if ($ba->transdetails && $ba->transdetails->count() > 0) {
                    $calculatedVolume = $ba->transdetails->sum('volume_isi');
                    if ($ba->volume_pengisian != $calculatedVolume) {
                        $ba->update(['volume_pengisian' => $calculatedVolume]);
                        $fixedCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil memperbaiki {$fixedCount} record volume_pengisian",
                'fixed_count' => $fixedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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

            // Cek apakah sudah ada BA Penerimaan BBM untuk tanggal dan kapal ini
            $existingBa = BbmKapaltrans::where('kapal_code', $kapal->code_kapal)
                ->where('tanggal_surat', $tanggalSurat)
                ->where('status_ba', 5)
                ->first();

            if ($existingBa) {
                return response()->json([
                    'success' => false,
                    'message' => 'BA Penerimaan Bbm untuk kapal dan tanggal ini sudah ada'
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
            'no_so' => 'required|string|max:50',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'transportasi' => 'required|array|min:1',
            'transportasi.*' => 'required|string|max:50',
            'no_do' => 'required|array|min:1',
            'no_do.*' => 'required|string|max:50',
            'volume_isi' => 'required|array|min:1',
            'volume_isi.*' => 'required|numeric|min:0',
            'keterangan' => 'required|array|min:1',
            'keterangan.*' => 'nullable|string|max:100',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pangkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:50',
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

            // Insert data utama ke bbm_kapaltrans
            $ba = BbmKapaltrans::create([
                'trans_id' => $newId,
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'status_ba' => 5, // BA Penerimaan BBM (sesuai project_ci)
                'penyedia' => $request->penyedia,
                'no_so' => $request->no_so,
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pangkalan' => $request->nama_staf_pangkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'status_trans' => 0, // Input
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert data detail transportasi ke bbm_transdetail
            // Get the highest bbm_transdetail_id and add 1
            $maxTransdetailId = BbmTransdetail::max('bbm_transdetail_id') ?? 0;

            for ($i = 0; $i < count($request->transportasi); $i++) {
                if (!empty($request->transportasi[$i])) {
                    $maxTransdetailId++; // Increment untuk setiap record

                    BbmTransdetail::create([
                        'bbm_transdetail_id' => $maxTransdetailId,
                        'nomor_surat' => $request->nomor_surat,
                        'transportasi' => $request->transportasi[$i],
                        'no_so' => $request->no_so,
                        'no_do' => $request->no_do[$i],
                        'volume_isi' => $request->volume_isi[$i],
                        'keterangan' => $request->keterangan[$i] ?? '',
                        'tanggalinput' => now(),
                        'userid' => (string)(auth()->user()->conf_user_id ?? 1),
                        'status_bayar' => 0
                    ]);
                }
            }

            // Hitung total volume dari detail transportasi
            $totalVolume = BbmTransdetail::getTotalVolume($request->nomor_surat);

            // Update volume_pengisian di tabel utama
            $ba->update(['volume_pengisian' => $totalVolume]);

            DB::commit();
            $ba->trans_id = $newId;

            return response()->json([
                'success' => true,
                'message' => 'BA Penerimaan Bbm berhasil dibuat',
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
    public function show($id)
    {
        try {
            $baPenerimaanBbm = BbmKapaltrans::where('status_ba', 5)
                ->with(['kapal.upt', 'transdetails'])
                ->find($id);

            if (!$baPenerimaanBbm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $baPenerimaanBbm
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the record first
        $baPenerimaanBbm = BbmKapaltrans::find($id);

        if (!$baPenerimaanBbm) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        // Check if nomor_surat is being changed
        $nomorSuratRules = 'required|string|max:50';
        if ($request->nomor_surat !== $baPenerimaanBbm->nomor_surat) {
            $nomorSuratRules .= '|unique:bbm_kapaltrans,nomor_surat,' . $baPenerimaanBbm->trans_id . ',trans_id';
        }

        $validator = Validator::make($request->all(), [
            'kapal_id' => 'required|exists:m_kapal,m_kapal_id',
            'nomor_surat' => $nomorSuratRules,
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'penyedia' => 'required|string|max:100',
            'no_so' => 'required|string|max:50',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'transportasi' => 'required|array|min:1',
            'transportasi.*' => 'required|string|max:50',
            'no_do' => 'required|array|min:1',
            'no_do.*' => 'required|string|max:50',
            'volume_isi' => 'required|array|min:1',
            'volume_isi.*' => 'required|numeric|min:0',
            'keterangan' => 'required|array|min:1',
            'keterangan.*' => 'nullable|string|max:100',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pangkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:50',
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

            // Update data utama
            $baPenerimaanBbm->update([
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'penyedia' => $request->penyedia,
                'no_so' => $request->no_so,
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pangkalan' => $request->nama_staf_pangkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'updated_at' => now(),
            ]);

            // Delete semua detail transportasi lama
            BbmTransdetail::where('nomor_surat', $request->nomor_surat)->delete();

            // Insert data detail transportasi baru
            // Get the highest bbm_transdetail_id and add 1
            $maxTransdetailId = BbmTransdetail::max('bbm_transdetail_id') ?? 0;

            for ($i = 0; $i < count($request->transportasi); $i++) {
                if (!empty($request->transportasi[$i])) {
                    $maxTransdetailId++; // Increment untuk setiap record

                    BbmTransdetail::create([
                        'bbm_transdetail_id' => $maxTransdetailId,
                        'nomor_surat' => $request->nomor_surat,
                        'transportasi' => $request->transportasi[$i],
                        'no_so' => $request->no_so,
                        'no_do' => $request->no_do[$i],
                        'volume_isi' => $request->volume_isi[$i],
                        'keterangan' => $request->keterangan[$i] ?? '',
                        'tanggalinput' => now(),
                        'userid' => (string)(auth()->user()->conf_user_id ?? 1),
                        'status_bayar' => 0
                    ]);
                }
            }

            // Hitung ulang total volume dari detail transportasi
            $totalVolume = BbmTransdetail::getTotalVolume($request->nomor_surat);

            // Update volume_pengisian di tabel utama
            $baPenerimaanBbm->update(['volume_pengisian' => $totalVolume]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Penerimaan Bbm berhasil diperbarui',
                'data' => $baPenerimaanBbm->load('kapal')
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
    public function destroy($id)
    {
        try {
            // Find the record first
            $baPenerimaanBbm = BbmKapaltrans::where('status_ba', 5)->find($id);

            if (!$baPenerimaanBbm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Cek status pembayaran di detail transportasi
            $hasPayment = BbmTransdetail::hasPayment($baPenerimaanBbm->nomor_surat);

            if ($hasPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah melakukan pembayaran'
                ], 400);
            }

            DB::beginTransaction();

            // Delete detail transportasi
            BbmTransdetail::where('nomor_surat', $baPenerimaanBbm->nomor_surat)->delete();

            // Delete data utama
            $baPenerimaanBbm->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Data gagal dihapus: ' . $e->getMessage()
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
     * Function: cetak_ba_pemenerimaan_bbm() - Semua elemen template sudah 100% sama persis termasuk:
     * - Header dengan logo KKP dan informasi UPT
     * - Judul "BERITA ACARA PENERIMAAN BBM"
     * - Format tanggal Indonesia (indo_date) dan jam dengan titik
     * - Pernyataan penerimaan BBM dari penyedia
     * - Font size dinamis berdasarkan jumlah detail (11px untuk 8-10 items, 12px untuk lainnya)
     * - Tabel detail transportasi dengan kolom (No, Transportasi, Nomor SO, Nomor DO, Volume, Keterangan)
     * - Row jumlah total volume
     * - Footer dengan 3 tanda tangan (KKM, Penyedia, Nakhoda) dan Staf UPT sebagai saksi
     * - Format filename "BA_Penerimaan_BBM_"
     * - Semua styling, spacing, dan layout sudah sama persis dengan project_ci
     */
    public function generatePdf($id)
    {
        try {
            // Find the record first
            $baPenerimaanBbm = BbmKapaltrans::find($id);

            if (!$baPenerimaanBbm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Load relationship data
            $baPenerimaanBbm->load(['kapal.upt', 'transdetails']);

            // Get data
            $data = $baPenerimaanBbm;
            $kapal = $data->kapal;
            $upt = $kapal ? $kapal->upt : null;
            $transdetails = $data->transdetails;

            // Format date sesuai project_ci (menggunakan indo_date format)
            $tanggalFormatted = $this->indo_date($data->tanggal_surat);
            $jamFormatted = str_replace(':', '.', $data->jam_surat);

            // Handle "An." prefix
            $anStaf = ($data->an_staf == 1 || $data->an_staf === true) ? 'An. ' : '';
            $anNakhoda = ($data->an_nakhoda == 1 || $data->an_nakhoda === true) ? 'An. ' : '';
            $anKkm = ($data->an_kkm == 1 || $data->an_kkm === true) ? 'An. ' : '';

            // Font size dinamis berdasarkan jumlah detail transportasi (sesuai project_ci)
            $countDetails = $transdetails->count();
            if ($countDetails == 8 || $countDetails == 9 || $countDetails == 10) {
                $fontSize = '11px';
            } else {
                $fontSize = '12px';
            }

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
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:' . $fontSize . '" border="0">
                    <tr>
                        <td width="100%" align="center">
                            <font size="12"><b><u>BERITA ACARA PENERIMAAN BBM</b></u></font>
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
                        <td width="100%" align="justify">Menyatakan bahwa telah menerima hasil pekerjaan pengadaan BBM ' . $data->keterangan_jenis_bbm . ' dari penyedia ' . $data->penyedia . ' :
                        </td>
                    </tr>
                </table>';

            // Tabel detail transportasi sesuai template project_ci
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:' . $fontSize . '" border="1">
                    <tr>
                        <td width="5%" align="center"><b>No</b></td>
                        <td align="center"><b>Transportasi</b></td>
                        <td align="center"><b>Nomor SO</b></td>
                        <td align="center"><b>Nomor DO</b></td>
                        <td align="center"><b>Volume (Liter)</b></td>
                        <td width="auto" align="center"><b>Keterangan</b></td>
                    </tr>';

            $i = 1;
            $totalVolume = 0;
            foreach ($transdetails as $detail) {
                $html .= '<tr>
                    <td width="5%" align="center">' . $i++ . '</td>
                    <td align="center">' . $detail->transportasi . '</td>
                    <td align="center">' . $data->no_so . '</td>
                    <td align="center">' . $detail->no_do . '</td>
                    <td align="center">' . number_format($detail->volume_isi) . ' Liter</td>
                    <td width="auto" align="center">' . $detail->keterangan . '</td>
                </tr>';
                $totalVolume += $detail->volume_isi;
            }

            $html .= '<tr>
                <td colspan="4" align="center">JUMLAH</td>
                <td align="center">' . number_format($totalVolume) . ' Liter</td>
                <td width="auto" align="center"></td>
            </tr>';

            $html .= '</table>';

            // Closing statement
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:' . $fontSize . '" border="0">
                    <tr>
                        <td width="100%" align="justify">Demikian Berita Acara Penerimaan BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.
                        </td>
                    </tr>
                </table>';

            // Footer signatures sesuai template project_ci (BA Penerimaan BBM)
            $html .= '<br><br><br>
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
                            <b><u>' . $data->nama_staf_pangkalan . '</u></b><br>
                            <b>NIP. ' . $data->nip_staf . '</b>
                        </td>
                    </tr>
                </table>';

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Add page 2 for images
            $this->addImagesPage($pdf, $data);

            // Generate filename (sesuai template project_ci)
            $filename = 'BA_Penerimaan_BBM_' . str_replace('/', '_', $data->nomor_surat) . '_' . date('Y-m-d_H-i-s');

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
     * Add images page to PDF (OPTIMIZED VERSION)
     */
    private function addImagesPage($pdf, $data)
    {
        // Add new page
        $pdf->AddPage('P', 'A4');

        // Header for images page
        $html = '<div style="text-align: center; margin-bottom: 0px;">
            <h3 style="font-size: 14px; font-weight: bold; margin: 0;">LAMPIRAN FOTO</h3>
            <p style="font-size: 10px; margin: 3px 0;">Berita Acara Penerimaan BBM</p>
            <p style="font-size: 10px; margin: 3px 0;">Nomor: ' . htmlspecialchars($data->nomor_surat) . '</p>
        </div>';

        // OPTIMIZATION: Pre-cache all image paths to avoid repeated file_exists() calls
        $imagePaths = $this->preloadImagePaths($data);

        // Main images section - Layout 3 kolom menggunakan table
        $html .= '<div style="margin-bottom: 1px;">';

        // Container untuk foto utama (3 kolom) menggunakan table
        $html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 1px;">
            <tr>';

        // Foto SO (Sales Order) - Use cached path
        if (!empty($imagePaths['foto_so'])) {
            $html .= '<td style="width: 33.33%; text-align: center; padding: 8px; vertical-align: top;">
                <h3 style="font-size: 9px; font-weight: bold; margin-bottom: 5px;">Foto Sales Order (SO)</h3>
                <img src="' . $imagePaths['foto_so'] . '" width="100" height="100">
            </td>';
        }

        // Foto dan TTD dari data utama - Use cached paths
        if (!empty($imagePaths['foto'])) {
            $html .= '<td style="width: 33.33%; text-align: center; padding: 8px; vertical-align: top;">
                <h3 style="font-size: 9px; font-weight: bold; margin-bottom: 5px;">Foto Berita Acara</h3>
                <img src="' . $imagePaths['foto'] . '" width="100" height="100">
            </td>';
        }

        if (!empty($imagePaths['ttd'])) {
            $html .= '<td style="width: 33.33%; text-align: center; padding: 8px; vertical-align: top;">
                <h3 style="font-size: 9px; font-weight: bold; margin-bottom: 5px;">Tanda Tangan</h3>
                <img src="' . $imagePaths['ttd'] . '" width="100" height="100">
            </td>';
        }

        $html .= '</tr></table>'; // Tutup table 3 kolom

        // Foto dari transdetails - OPTIMIZED: Build all HTML at once
        if ($data->transdetails && $data->transdetails->count() > 0) {
            $html .= '<div style="margin-bottom: 1px;">
                <h3 style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">Foto Detail Transportasi</h3>';

            foreach ($data->transdetails as $index => $detail) {
                $html .= '<div style="margin-bottom: 0px;">
                    <h4 style="font-size: 10px; font-weight: bold; margin-bottom: 0px;">
                        Transportasi ' . ($index + 1) . ': ' . htmlspecialchars($detail->transportasi) .
                    ' (DO: ' . htmlspecialchars($detail->no_do) . ') - Volume: ' . number_format($detail->volume_isi, 0, ',', '.') . ' Liter
                    </h4>';

                // Container untuk foto detail (3 kolom) menggunakan table
                $html .= '<table style="width: 100%; border-collapse: collapse;">
                    <tr>';

                // Use cached paths for detail photos
                $detailKey = 'detail_' . $index;

                // Foto DO
                if (!empty($imagePaths[$detailKey . '_foto_do'])) {
                    $html .= '<td style="width: 33.33%; text-align: center; padding: 5px; vertical-align: top;">
                        <p style="font-size: 8px; font-weight: bold; margin-bottom: 0px;">Foto Delivery Order (DO)</p>
                        <img src="' . $imagePaths[$detailKey . '_foto_do'] . '" width="100" height="100">
                    </td>';
                }

                // Foto Segel
                if (!empty($imagePaths[$detailKey . '_foto_segel'])) {
                    $html .= '<td style="width: 33.33%; text-align: center; padding: 5px; vertical-align: top;">
                        <p style="font-size: 8px; font-weight: bold; margin-bottom: 0px;">Foto Segel</p>
                        <img src="' . $imagePaths[$detailKey . '_foto_segel'] . '" width="100" height="100">
                    </td>';
                }

                // Foto Volume
                if (!empty($imagePaths[$detailKey . '_foto_volume'])) {
                    $html .= '<td style="width: 33.33%; text-align: center; padding: 5px; vertical-align: top;">
                        <p style="font-size: 8px; font-weight: bold; margin-bottom: 0px;">Foto Volume</p>
                        <img src="' . $imagePaths[$detailKey . '_foto_volume'] . '" width="100" height="100">
                    </td>';
                }

                $html .= '</tr></table>'; // Tutup table 3 kolom untuk detail
                $html .= '</div>'; // Tutup container transportasi
            }

            $html .= '</div>';
        }

        $html .= '</div>';

        // Write HTML to PDF
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    /**
     * Preload all image paths to avoid repeated file_exists() calls
     * OPTIMIZATION: Cache all image paths in one go
     */
    private function preloadImagePaths($data)
    {
        $imagePaths = [];

        // Main images (foto_so, foto, ttd)
        if ($data->foto_so) {
            $imagePaths['foto_so'] = $this->getFullImagePath($data->foto_so, 'foto_so', $data->created_at);
        }

        if ($data->foto) {
            $imagePaths['foto'] = $this->getFullImagePath($data->foto, 'foto');
        }

        if ($data->ttd) {
            $imagePaths['ttd'] = $this->getFullImagePath($data->ttd, 'ttd');
        }

        // Detail images from transdetails
        if ($data->transdetails && $data->transdetails->count() > 0) {
            foreach ($data->transdetails as $index => $detail) {
                $detailKey = 'detail_' . $index;

                if ($detail->foto_do) {
                    $imagePaths[$detailKey . '_foto_do'] = $this->getFullImagePath($detail->foto_do, 'foto_do', $detail->tanggalinput);
                }

                if ($detail->foto_segel) {
                    $imagePaths[$detailKey . '_foto_segel'] = $this->getFullImagePath($detail->foto_segel, 'foto_segel', $detail->tanggalinput);
                }

                if ($detail->foto_volume) {
                    $imagePaths[$detailKey . '_foto_volume'] = $this->getFullImagePath($detail->foto_volume, 'foto_volume', $detail->tanggalinput);
                }
            }
        }

        return $imagePaths;
    }

    /**
     * Get full image path for PDF (local file system path) - OPTIMIZED
     */
    private function getFullImagePath($filename, $type, $date = null)
    {
        // If filename is null or empty, return empty string
        if (empty($filename)) {
            return '';
        }

        // If already a full URL, extract filename
        if (filter_var($filename, FILTER_VALIDATE_URL)) {
            $filename = basename($filename);
        }

        // Determine the correct path based on type
        switch ($type) {
            case 'foto_so':
                // foto_so stored in uploads/ba-foto/YYYY/MM/DD/
                $datePath = $date ? date('Y/m/d', strtotime($date)) : date('Y/m/d');
                $fullPath = public_path('uploads/ba-foto/' . $datePath . '/' . $filename);
                break;

            case 'foto':
            case 'ttd':
                // foto and ttd stored in uploads/ba-penerimaan-bbm/
                $fullPath = public_path('uploads/ba-penerimaan-bbm/' . $filename);
                break;

            case 'foto_do':
            case 'foto_segel':
            case 'foto_volume':
                // transdetails foto stored in uploads/ba-foto/YYYY/MM/DD/
                $datePath = $date ? date('Y/m/d', strtotime($date)) : date('Y/m/d');
                $fullPath = public_path('uploads/ba-foto/' . $datePath . '/' . $filename);
                break;

            default:
                $fullPath = public_path('uploads/' . $filename);
        }

        // OPTIMIZATION: Only check file_exists once and cache the result
        static $fileCache = [];
        $cacheKey = $fullPath;

        if (!isset($fileCache[$cacheKey])) {
            $fileCache[$cacheKey] = file_exists($fullPath) ? $fullPath : '';
        }

        return $fileCache[$cacheKey];
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
            $ba = BbmKapaltrans::where('status_ba', 5)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 5)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 5)->findOrFail($id);

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
