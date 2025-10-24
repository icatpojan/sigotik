<?php

namespace App\Http\Controllers\Web\hibah;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\BbmTransdetail;
use App\Models\MKapal;
use App\Models\MUpt;
use App\Models\MPersetujuan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
class BaPemberiHibahBbmKapalPengawasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kapals = MKapal::all();
        $persetujuans = MPersetujuan::all();

        return view('ba-pemberi-hibah-bbm-kapal-pengawas.index', compact('kapals', 'persetujuans'));
    }

    /**
     * Get BA Pemberi Hibah BBM Kapal Pengawas data via AJAX
     */
    public function getData(Request $request)
    {
        $query = BbmKapaltrans::with(['kapal'])
            ->where('status_ba', 14); // BA Pemberi Hibah BBM Kapal Pengawas

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
        $codeKapal = $request->code_kapal;

        // Cari berdasarkan ID atau code_kapal
        if ($kapalId) {
            $kapal = MKapal::with('upt')->find($kapalId);
        } elseif ($codeKapal) {
            $kapal = MKapal::with('upt')->where('code_kapal', $codeKapal)->first();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kapal ID atau code kapal harus disediakan'
            ], 400);
        }

        if (!$kapal) {
            return response()->json([
                'success' => false,
                'message' => 'Kapal tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'm_kapal_id' => $kapal->m_kapal_id,
                'code_kapal' => $kapal->code_kapal,
                'alamat_upt' => $kapal->upt ? $kapal->upt->alamat1 : '',
                'zona_waktu_upt' => $kapal->upt ? $kapal->upt->zona_waktu_upt : 'WIB',
                'kota' => $kapal->upt ? $kapal->upt->kota : '',
                'pangkat_nakoda' =>  $kapal->jabatan_nakoda,
                'nama_petugas' => $kapal->upt ? $kapal->upt->nama_petugas : '',
                'nip_petugas' => $kapal->upt ? $kapal->upt->nip_petugas : '',
                'jabatan_petugas' => $kapal->upt ? $kapal->upt->jabatan_petugas : '',
                'nama_nakoda' => $kapal->nama_nakoda,
                'nip_nakoda' => $kapal->nip_nakoda,
                'nama_kkm' => $kapal->nama_kkm,
                'nip_kkm' => $kapal->nip_kkm,
            ]
        ]);
    }

    /**
     * Get BBM/sounding data for volume sebelum pengisian
     */
    public function getVolumeSounding(Request $request)
    {
        $tanggalSurat = $request->tanggal_surat;
        $kapalId = $request->kapal_id;

        if (!$tanggalSurat || !$kapalId) {
            return response()->json([
                'jml' => 0,
                'nomor_surat' => '',
                'pesan' => 'Tanggal dan kapal harus disediakan',
                'volume_sisa' => 0
            ]);
        }

        try {
            // Cari kapal berdasarkan ID
            $kapal = MKapal::find($kapalId);
            if (!$kapal) {
                return response()->json([
                    'jml' => 0,
                    'nomor_surat' => '',
                    'pesan' => 'Kapal tidak ditemukan',
                    'volume_sisa' => 0
                ]);
            }

            // Cari BA Peminjaman BBM (status_ba = 2) pada tanggal tersebut
            $baPeminjaman = BbmKapaltrans::where('kapal_code', $kapal->code_kapal)
                ->where('tanggal_surat', $tanggalSurat)
                ->where('status_ba', 2) // BA Peminjaman BBM
                ->orderBy('tanggal_surat', 'desc')
                ->orderBy('jam_surat', 'desc')
                ->first();

            if (!$baPeminjaman) {
                return response()->json([
                    'jml' => 0,
                    'nomor_surat' => '',
                    'pesan' => 'Hari ini anda belum melakukan sounding/pengukuran',
                    'volume_sisa' => 0
                ]);
            }

            // Cari BA Pengembalian BBM (status_ba = 3) yang terkait dengan BA Peminjaman
            $baPengembalian = BbmKapaltrans::where('kapal_code', $kapal->code_kapal)
                ->where('tanggal_surat', $tanggalSurat)
                ->where('status_ba', 3) // BA Pengembalian BBM
                ->where('link_modul_ba', $baPeminjaman->nomor_surat)
                ->orderBy('tanggal_surat', 'desc')
                ->orderBy('jam_surat', 'desc')
                ->first();

            if (!$baPengembalian) {
                return response()->json([
                    'jml' => 0,
                    'nomor_surat' => '',
                    'pesan' => 'Hari ini anda belum melakukan penggunaan bbm',
                    'volume_sisa' => 0
                ]);
            }

            return response()->json([
                'jml' => 1,
                'nomor_surat' => $baPeminjaman->nomor_surat,
                'pesan' => '',
                'volume_sisa' => number_format($baPengembalian->volume_sisa, 2, '.', '')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'jml' => 0,
                'nomor_surat' => '',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'volume_sisa' => 0
            ]);
        }
    }

    /**
     * Get kapal penerima hibah data
     */
    public function getKapalPenerimaData(Request $request)
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
                'nama_kapal' => $kapal->nama_kapal,
                'nama_nakoda' => $kapal->nama_nakoda,
                'nip_nakoda' => $kapal->nip_nakoda,
                'pangkat_nakoda' => $kapal->jabatan_nakoda,
                'nama_kkm' => $kapal->nama_kkm,
                'nip_kkm' => $kapal->nip_kkm,
            ]
        ]);
    }

    /**
     * Get persetujuan data
     */
    public function getPersetujuanData(Request $request)
    {
        $persetujuanId = $request->persetujuan_id;

        $persetujuan = MPersetujuan::find($persetujuanId);

        if (!$persetujuan) {
            return response()->json([
                'success' => false,
                'message' => 'Persetujuan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nomor_persetujuan' => $persetujuan->deskripsi_persetujuan,
                'tanggal_persetujuan' => now()->format('Y-m-d'), // Default tanggal hari ini
            ]
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
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'kapal_penerima_id' => 'required|exists:m_kapal,m_kapal_id',
            'persetujuan_id' => 'required|exists:m_persetujuan,id',
            'nomor_persetujuan' => 'required|string|max:50',
            'tanggal_persetujuan' => 'required|date',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'volume_sebelum' => 'nullable|numeric|min:0',
            'volume_pemakaian' => 'required|numeric|min:0',
            'volume_sisa' => 'nullable|numeric|min:0',
            'sebab_temp' => 'required|string',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pangkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:50',
            'pangkat_nahkoda' => 'nullable|string|max:50',
            'nip_nahkoda' => 'nullable|string|max:20',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'nama_nahkoda_penerima' => 'nullable|string|max:30',
            'pangkat_nahkoda_penerima' => 'nullable|string|max:50',
            'nip_nahkoda_penerima' => 'nullable|string|max:20',
            'nama_kkm_penerima' => 'nullable|string|max:30',
            'nip_kkm_penerima' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
            'an_nakhoda_penerima' => 'nullable|in:0,1',
            'an_kkm_penerima' => 'nullable|in:0,1',
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
            $kapalPenerima = MKapal::find($request->kapal_penerima_id);
            $persetujuan = MPersetujuan::find($request->persetujuan_id);

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
                'status_ba' => 14, // BA Pemberi Hibah BBM Kapal Pengawas
                'kapal_code_temp' => $kapalPenerima->code_kapal,
                'link_modul_ba' => $request->nomor_persetujuan,
                'nomer_persetujuan' => $request->nomor_persetujuan,
                'tgl_persetujuan' => $request->tanggal_persetujuan,
                'm_persetujuan_id' => $persetujuan->id,
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'volume_sebelum' => $request->volume_sebelum ?? 0,
                'volume_pemakaian' => $request->volume_pemakaian,
                'volume_sisa' => $request->volume_sisa ?? 0,
                'sebab_temp' => $request->sebab_temp,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pangkalan' => $request->nama_staf_pangkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'pangkat_nahkoda' => $request->pangkat_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'nama_nahkoda_temp' => $request->nama_nahkoda_penerima,
                'pangkat_nahkoda_temp' => $request->pangkat_nahkoda_penerima,
                'nip_nahkoda_temp' => $request->nip_nahkoda_penerima,
                'nama_kkm_temp' => $request->nama_kkm_penerima,
                'nip_kkm_temp' => $request->nip_kkm_penerima,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'an_nakhoda_temp' => (int)($request->an_nakhoda_penerima == '1'),
                'an_kkm_temp' => (int)($request->an_kkm_penerima == '1'),
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'status_trans' => 0, // Input
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Pemberi Hibah BBM Kapal Pengawas berhasil dibuat',
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
            $baPemberiHibah = BbmKapaltrans::where('status_ba', 14)
                ->with(['kapal.upt'])
                ->find($id);

            if (!$baPemberiHibah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $baPemberiHibah
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
        $baPemberiHibah = BbmKapaltrans::find($id);

        if (!$baPemberiHibah) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Check if nomor_surat is being changed
        $nomorSuratRules = 'required|string|max:50';
        if ($request->nomor_surat !== $baPemberiHibah->nomor_surat) {
            $nomorSuratRules .= '|unique:bbm_kapaltrans,nomor_surat,' . $baPemberiHibah->trans_id . ',trans_id';
        }

        $validator = Validator::make($request->all(), [
            'kapal_id' => 'required|exists:m_kapal,m_kapal_id',
            'nomor_surat' => $nomorSuratRules,
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'kapal_penerima_id' => 'required|exists:m_kapal,m_kapal_id',
            'persetujuan_id' => 'required|exists:m_persetujuan,id',
            'nomor_persetujuan' => 'required|string|max:50',
            'tanggal_persetujuan' => 'required|date',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'volume_sebelum' => 'nullable|numeric|min:0',
            'volume_pemakaian' => 'required|numeric|min:0',
            'volume_sisa' => 'nullable|numeric|min:0',
            'sebab_temp' => 'required|string',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pangkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:50',
            'pangkat_nahkoda' => 'nullable|string|max:50',
            'nip_nahkoda' => 'nullable|string|max:20',
            'nama_kkm' => 'nullable|string|max:30',
            'nip_kkm' => 'nullable|string|max:20',
            'nama_nahkoda_penerima' => 'nullable|string|max:30',
            'pangkat_nahkoda_penerima' => 'nullable|string|max:50',
            'nip_nahkoda_penerima' => 'nullable|string|max:20',
            'nama_kkm_penerima' => 'nullable|string|max:30',
            'nip_kkm_penerima' => 'nullable|string|max:20',
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
            'an_nakhoda_penerima' => 'nullable|in:0,1',
            'an_kkm_penerima' => 'nullable|in:0,1',
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
            $kapalPenerima = MKapal::find($request->kapal_penerima_id);
            $persetujuan = MPersetujuan::find($request->persetujuan_id);

            // Update data utama
            $baPemberiHibah->update([
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'kapal_code_temp' => $kapalPenerima->code_kapal,
                'link_modul_ba' => $request->nomor_persetujuan,
                'nomer_persetujuan' => $request->nomor_persetujuan,
                'tgl_persetujuan' => $request->tanggal_persetujuan,
                'm_persetujuan_id' => $persetujuan->id,
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'volume_sebelum' => $request->volume_sebelum ?? 0,
                'volume_pemakaian' => $request->volume_pemakaian,
                'volume_sisa' => $request->volume_sisa ?? 0,
                'sebab_temp' => $request->sebab_temp,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pangkalan' => $request->nama_staf_pangkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'pangkat_nahkoda' => $request->pangkat_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'nama_nahkoda_temp' => $request->nama_nahkoda_penerima,
                'pangkat_nahkoda_temp' => $request->pangkat_nahkoda_penerima,
                'nip_nahkoda_temp' => $request->nip_nahkoda_penerima,
                'nama_kkm_temp' => $request->nama_kkm_penerima,
                'nip_kkm_temp' => $request->nip_kkm_penerima,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'an_nakhoda_temp' => (int)($request->an_nakhoda_penerima == '1'),
                'an_kkm_temp' => (int)($request->an_kkm_penerima == '1'),
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Pemberi Hibah BBM Kapal Pengawas berhasil diperbarui',
                'data' => $baPemberiHibah->load('kapal')
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
            $baPemberiHibah = BbmKapaltrans::where('status_ba', 14)->find($id);

            if (!$baPemberiHibah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Delete data utama
            $baPemberiHibah->delete();

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
     */
    public function generatePdf($id)
    {
        try {
            // Find the record first
            $baPemberiHibah = BbmKapaltrans::find($id);

            if (!$baPemberiHibah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Load relationship data
            $baPemberiHibah->load(['kapal.upt']);

            // Get persetujuan data
            $persetujuan = MPersetujuan::where('deskripsi_persetujuan', $baPemberiHibah->link_modul_ba)->first();

            // Get data
            $data = $baPemberiHibah;
            $kapal = $data->kapal;
            $upt = $kapal ? $kapal->upt : null;

            // Format date sesuai project_ci
            $tanggalFormatted = $this->indo_date($data->tanggal_surat);
            $jamFormatted = str_replace(':', '.', $data->jam_surat);

            // Handle "An." prefix
            $anStaf = ($data->an_staf == 1 || $data->an_staf === true) ? 'An. ' : '';
            $anNakhoda = ($data->an_nakhoda == 1 || $data->an_nakhoda === true) ? 'An. ' : '';
            $anKkm = ($data->an_kkm == 1 || $data->an_kkm === true) ? 'An. ' : '';
            $anNakhodaPenerima = ($data->an_nakhoda_temp == 1 || $data->an_nakhoda_temp === true) ? 'An. ' : '';
            $anKkmPenerima = ($data->an_kkm_temp == 1 || $data->an_kkm_temp === true) ? 'An. ' : '';

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

            // Get nama kapal penerima
            $kapalPenerima = MKapal::where('code_kapal', $data->kapal_code_temp)->first();
            $namaKapalPenerima = $kapalPenerima ? $kapalPenerima->nama_kapal : $data->kapal_code_temp;

            // Content - Format sesuai CI dengan struktur Pihak I dan Pihak II
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td width="100%" align="center">
                            <font size="12"><b><u>BERITA ACARA HIBAH BBM ANTAR KAPAL PENGAWAS PERIKANAN</b></u></font>
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
                        <td width="50%" align="justify">' . ($data->pangkat_nahkoda_temp ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="20%" align="justify">Jabatan</td>
                        <td width="3%" align="justify">:</td>
                        <td width="50%" align="justify">Nakhoda KP ' . $namaKapalPenerima . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="80%" align="justify">Selanjutnya disebut sebagai <b>Pihak I selaku penerima hibah BBM</b></td>
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
                        <td width="50%" align="justify">' . ($data->pangkat_nahkoda ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="20%" align="justify">Jabatan</td>
                        <td width="3%" align="justify">:</td>
                        <td width="50%" align="justify">Nakhoda KP ' . ($kapal ? $kapal->nama_kapal : '') . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="80%" align="justify">Selanjutnya disebut sebagai <b>Pihak II selaku pemberi hibah BBM</b></td>
                    </tr>
                </table>';

            // Narrative section
            $html .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100%" align="justify">Pada hari ini ' . $tanggalFormatted . ' pukul ' . $jamFormatted . ' ' . $data->zona_waktu_surat . ' bertempat di ' . $data->lokasi_surat . ' berdasarkan Surat Persetujuan dari ' . ($persetujuan ? $persetujuan->deskripsi_persetujuan : '') . '
                        Nomor ' . $data->nomer_persetujuan . ' tanggal ' . $this->indo_date($data->tgl_persetujuan) . ', telah dilakukan hibah BBM ' . $data->keterangan_jenis_bbm . ' dari PIHAK II ke PIHAK I sebanyak <b>' . number_format($data->volume_pengisian) . '</b> liter. Adapun hibah BBM ini di kerenakan <b>' . ($data->sebab_temp ?? '-') . '</b>
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
                        <td width="100%" align="justify">Demikian Berita Acara Hibah BBM ini dibuat dengan sebenar – benarnya untuk dapat dijadikan sebagai bahan keterangan dan dipergunakan sebagaimana mestinya.
                        </td>
                    </tr>
                </table>';

            // Footer signatures - Format sesuai CI
            $html .= '<br><br>
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
                            <b>' . $anNakhodaPenerima . ' Nakhoda KP. ' . $namaKapalPenerima . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_nahkoda_temp . '</u></b><br>
                            <b>NIP. ' . $data->nip_nahkoda_temp . '</b><br>
                        </td>
                        <td width="20%" align="center"></td>
                        <td width="40%" align="center">
                            <b>' . $anNakhoda . ' Nakhoda KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_nahkoda . '</u></b><br>
                            <b>NIP. ' . $data->nip_nahkoda . '</b>
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" align="center">
                            <b>' . $anKkmPenerima . ' KKM KP. ' . $namaKapalPenerima . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_kkm_temp . '</u></b><br>
                            <b>NIP. ' . $data->nip_kkm_temp . '</b><br>
                        </td>
                        <td width="20%" align="center">
                            <b><br><br>Menyaksikan:</b><br>
                            <b>' . $anStaf . ' ' . $data->jabatan_staf_pangkalan . '</b><br><br><br><br><br>
                        </td>
                        <td width="40%" align="center">
                            <b>' . $anKkm . ' KKM KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_kkm . '</u></b><br>
                            <b>NIP. ' . $data->nip_kkm . '</b>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" align="center"></td>
                        <td width="40%" align="center">
                            <b><u>' . $data->nama_staf_pangkalan . '</u></b><br>
                            <b>NIP. ' . $data->nip_staf . '</b>
                        </td>
                        <td width="30%" align="center"></td>
                    </tr>
                </table>';

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generate filename
            $filename = 'BA_Pemberi_Hibah_BBM_Kapal_Pengawas_' . str_replace('/', '_', $data->nomor_surat) . '_' . date('Y-m-d_H-i-s');

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
            $ba = BbmKapaltrans::where('status_ba', 14)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 14)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 14)->findOrFail($id);

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
