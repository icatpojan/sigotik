<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BaPenerimaanPengembalianPinjamanBbmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kapals = MKapal::all();

        return view('ba-penerimaan-pengembalian-pinjaman-bbm.index', compact('kapals'));
    }

    /**
     * Get BA Penerimaan Pengembalian Pinjaman BBM data via AJAX
     */
    public function getData(Request $request)
    {
        $query = BbmKapaltrans::with(['kapal'])
            ->where('status_ba', 13); // BA Penerimaan Pengembalian Pinjaman BBM

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
     * Get BA data for linking (BA Pengembalian Pinjaman BBM)
     */
    public function getBaData(Request $request)
    {
        // Jika ada trans_id, ambil data BA Pengembalian berdasarkan trans_id
        if ($request->has('trans_id') && $request->trans_id) {
            try {
                $baPengembalian = BbmKapaltrans::where('trans_id', $request->trans_id)
                    ->where('status_ba', 12) // BA Pengembalian Pinjaman BBM
                    ->first();

                if ($baPengembalian) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'trans_id' => $baPengembalian->trans_id,
                            'nomor_surat' => $baPengembalian->nomor_surat,
                            'tanggal_surat' => $baPengembalian->tanggal_surat,
                            'volume_pemakaian' => $baPengembalian->volume_pemakaian,
                            'keterangan_jenis_bbm' => $baPengembalian->keterangan_jenis_bbm,
                            'sebab_temp' => $baPengembalian->sebab_temp,
                            'kapal_code' => $baPengembalian->kapal_code,
                            'kapal_code_temp' => $baPengembalian->kapal_code_temp,
                            'nama_nahkoda_temp' => $baPengembalian->nama_nahkoda_temp,
                            'pangkat_nahkoda_temp' => $baPengembalian->pangkat_nahkoda_temp,
                            'nip_nahkoda_temp' => $baPengembalian->nip_nahkoda_temp,
                            'nama_kkm_temp' => $baPengembalian->nama_kkm_temp,
                            'nip_kkm_temp' => $baPengembalian->nip_kkm_temp,
                            'an_nakhoda_temp' => $baPengembalian->an_nakhoda_temp,
                            'an_kkm_temp' => $baPengembalian->an_kkm_temp,
                            'nama_nakoda' => $baPengembalian->nama_nakoda,
                            'pangkat_nahkoda' => $baPengembalian->pangkat_nahkoda,
                            'nip_nakoda' => $baPengembalian->nip_nakoda,
                            'nama_kkm' => $baPengembalian->nama_kkm,
                            'nip_kkm' => $baPengembalian->nip_kkm,
                            'an_nakhoda' => $baPengembalian->an_nakhoda,
                            'an_kkm' => $baPengembalian->an_kkm,
                        ]
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'BA Pengembalian Pinjaman tidak ditemukan'
                    ], 404);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }

        // Jika tidak ada trans_id, ambil daftar BA Pengembalian berdasarkan kapal
        $kapalId = $request->kapal_id;

        if (!$kapalId) {
            return response()->json([
                'success' => false,
                'message' => 'Kapal ID diperlukan'
            ], 400);
        }

        try {
            $kapal = MKapal::find($kapalId);

            if (!$kapal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kapal tidak ditemukan'
                ], 404);
            }

            // Cari BA Pengembalian Pinjaman BBM (status_ba = 12) yang belum diterima kembali (status_temp = 0)
            // Kapal saat ini adalah yang menerima pengembalian, jadi kapal_code_temp harus sama dengan code_kapal
            $baPengembalian = BbmKapaltrans::where('kapal_code_temp', $kapal->code_kapal)
                ->where('status_ba', 12) // BA Pengembalian Pinjaman BBM
                ->where('status_temp', null) // Belum diterima kembali
                ->orderBy('tanggal_surat', 'desc')
                ->orderBy('jam_surat', 'desc')
                ->get();

            if ($baPengembalian->count() > 0) {
                return response()->json([
                    'success' => true,
                    'data' => $baPengembalian->map(function ($item) {
                        return [
                            'trans_id' => $item->trans_id,
                            'nomor_surat' => $item->nomor_surat,
                            'tanggal_surat' => $item->tanggal_surat,
                            'volume_pemakaian' => $item->volume_pemakaian,
                            'keterangan_jenis_bbm' => $item->keterangan_jenis_bbm,
                            'sebab_temp' => $item->sebab_temp,
                            'kapal_code' => $item->kapal_code,
                            'kapal_code_temp' => $item->kapal_code_temp,
                            'nama_nahkoda_temp' => $item->nama_nahkoda_temp,
                            'pangkat_nahkoda_temp' => $item->pangkat_nahkoda_temp,
                            'nip_nahkoda_temp' => $item->nip_nahkoda_temp,
                            'nama_kkm_temp' => $item->nama_kkm_temp,
                            'nip_kkm_temp' => $item->nip_kkm_temp,
                            'an_nakhoda_temp' => $item->an_nakhoda_temp,
                            'an_kkm_temp' => $item->an_kkm_temp,
                        ];
                    })
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => []
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
            'trans_id_peminjam' => 'required|exists:bbm_kapaltrans,trans_id',
            'volume_pengisian' => 'required|numeric|min:0',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'sebab_temp' => 'nullable|string|max:255',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pagkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:30',
            'pangkat_nahkoda' => 'nullable|string|max:30',
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

            // Get kapal data dan data BA Pengembalian Pinjaman
            $kapal = MKapal::find($request->kapal_id);
            $baPengembalian = BbmKapaltrans::find($request->trans_id_peminjam);

            // Get the highest ID and add 1
            $maxId = BbmKapaltrans::max('trans_id') ?? 0;
            $newId = $maxId + 1;

            // Calculate volume sisa (volume sebelum + volume yang dikembalikan)
            $volumeSisa = $baPengembalian->volume_sisa + $request->volume_pengisian;

            $ba = BbmKapaltrans::create([
                'trans_id' => $newId,
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'status_ba' => 13, // BA Penerimaan Pengembalian Pinjaman BBM
                'volume_pengisian' => $request->volume_pengisian,
                'volume_sebelum' => $baPengembalian->volume_sisa,
                'volume_sisa' => $volumeSisa,
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'sebab_temp' => $request->sebab_temp,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'pangkat_nahkoda' => $request->pangkat_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'kapal_code_temp' => $baPengembalian->kapal_code_temp,
                'nama_nahkoda_temp' => $baPengembalian->nama_nahkoda_temp,
                'pangkat_nahkoda_temp' => $baPengembalian->pangkat_nahkoda_temp,
                'nip_nahkoda_temp' => $baPengembalian->nip_nahkoda_temp,
                'nama_kkm_temp' => $baPengembalian->nama_kkm_temp,
                'nip_kkm_temp' => $baPengembalian->nip_kkm_temp,
                'an_nakhoda_temp' => $baPengembalian->an_nakhoda_temp,
                'an_kkm_temp' => $baPengembalian->an_kkm_temp,
                'link_modul_temp' => $baPengembalian->nomor_surat,
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'status_trans' => 0, // Input
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update status_temp di BA Pengembalian Pinjaman dan link_modul_ba
            $baPengembalian->update([
                'status_temp' => 1,
                'link_modul_ba' => $request->nomor_surat
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Penerimaan Pengembalian Pinjaman BBM berhasil dibuat',
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
    public function show(BbmKapaltrans $baPenerimaanPengembalian)
    {
        // Get data BA Pengembalian yang di-link
        $baPengembalian = null;
        if ($baPenerimaanPengembalian->link_modul_temp) {
            $baPengembalian = BbmKapaltrans::where('nomor_surat', $baPenerimaanPengembalian->link_modul_temp)
                ->where('status_ba', 12)
                ->first();
        }

        $data = $baPenerimaanPengembalian->load('kapal.upt');

        // Add data kapal peminjam dari BA Pengembalian
        if ($baPengembalian) {
            $data->trans_id_peminjam = $baPengembalian->trans_id;
            $data->link_modul_temp = $baPengembalian->nomor_surat;
            $data->volume_sebelum = $baPengembalian->volume_sisa;
            $data->keterangan_jenis_bbm = $baPengembalian->keterangan_jenis_bbm;
            $data->sebab_temp = $baPengembalian->sebab_temp;
            $data->kapal_code_temp = $baPengembalian->kapal_code_temp;
            $data->nama_nahkoda_temp = $baPengembalian->nama_nahkoda_temp;
            $data->pangkat_nahkoda_temp = $baPengembalian->pangkat_nahkoda_temp;
            $data->nip_nahkoda_temp = $baPengembalian->nip_nahkoda_temp;
            $data->nama_kkm_temp = $baPengembalian->nama_kkm_temp;
            $data->nip_kkm_temp = $baPengembalian->nip_kkm_temp;
            $data->an_nakhoda_temp = $baPengembalian->an_nakhoda_temp;
            $data->an_kkm_temp = $baPengembalian->an_kkm_temp;
        }

        // Convert to array to ensure all data is included
        $responseData = $data->toArray();

        // Add kapal peminjam data to response array
        if ($baPengembalian) {
            $responseData['trans_id_peminjam'] = $baPengembalian->trans_id;
            $responseData['link_modul_temp'] = $baPengembalian->nomor_surat;
            $responseData['volume_sebelum'] = $baPengembalian->volume_sisa;
            $responseData['keterangan_jenis_bbm'] = $baPengembalian->keterangan_jenis_bbm;
            $responseData['sebab_temp'] = $baPengembalian->sebab_temp;
            $responseData['kapal_code_temp'] = $baPengembalian->kapal_code_temp;
            $responseData['nama_nahkoda_temp'] = $baPengembalian->nama_nahkoda_temp;
            $responseData['pangkat_nahkoda_temp'] = $baPengembalian->pangkat_nahkoda_temp;
            $responseData['nip_nahkoda_temp'] = $baPengembalian->nip_nahkoda_temp;
            $responseData['nama_kkm_temp'] = $baPengembalian->nama_kkm_temp;
            $responseData['nip_kkm_temp'] = $baPengembalian->nip_kkm_temp;
            $responseData['an_nakhoda_temp'] = $baPengembalian->an_nakhoda_temp;
            $responseData['an_kkm_temp'] = $baPengembalian->an_kkm_temp;
        }

        return response()->json([
            'success' => true,
            'data' => $responseData
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BbmKapaltrans $baPenerimaanPengembalian)
    {
        $validator = Validator::make($request->all(), [
            'kapal_id' => 'required|exists:m_kapal,m_kapal_id',
            'nomor_surat' => 'required|string|max:50|unique:bbm_kapaltrans,nomor_surat,' . $baPenerimaanPengembalian->trans_id . ',trans_id',
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string',
            'trans_id_peminjam' => 'required|exists:bbm_kapaltrans,trans_id',
            'volume_pengisian' => 'required|numeric|min:0',
            'keterangan_jenis_bbm' => 'required|string|max:50',
            'sebab_temp' => 'nullable|string|max:255',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30',
            'nama_staf_pagkalan' => 'nullable|string|max:30',
            'nip_staf' => 'nullable|string|max:20',
            'nama_nahkoda' => 'nullable|string|max:30',
            'pangkat_nahkoda' => 'nullable|string|max:30',
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

            // Get BA Pengembalian data
            $baPengembalian = BbmKapaltrans::find($request->trans_id_peminjam);
            if (!$baPengembalian) {
                return response()->json([
                    'success' => false,
                    'message' => 'BA Pengembalian Pinjaman tidak ditemukan'
                ], 404);
            }

            // Calculate volume sisa
            $volumeSisa = $baPengembalian->volume_sisa + $request->volume_pengisian;

            $baPenerimaanPengembalian->update([
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'volume_sisa' => $volumeSisa,
                'volume_sebelum' => $baPengembalian->volume_sisa,
                'volume_pengisian' => $request->volume_pengisian,
                'keterangan_jenis_bbm' => $request->keterangan_jenis_bbm,
                'sebab_temp' => $request->sebab_temp,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'pangkat_nahkoda' => $request->pangkat_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'),
                'an_nakhoda' => (int)($request->an_nakhoda == '1'),
                'an_kkm' => (int)($request->an_kkm == '1'),
                'link_modul_temp' => $baPengembalian->nomor_surat,
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BA Penerimaan Pengembalian Pinjaman BBM berhasil diperbarui',
                'data' => $baPenerimaanPengembalian->load('kapal')
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
    public function destroy(BbmKapaltrans $baPenerimaanPengembalian)
    {
        try {
            $baPenerimaanPengembalian->delete();

            return response()->json([
                'success' => true,
                'message' => 'BA Penerimaan Pengembalian Pinjaman BBM berhasil dihapus'
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
    public function generatePdf(BbmKapaltrans $baPenerimaanPengembalian)
    {
        try {
            // Load relationship data
            $baPenerimaanPengembalian->load(['kapal.upt']);

            // Get data
            $data = $baPenerimaanPengembalian;
            $kapal = $data->kapal;
            $upt = $kapal ? $kapal->upt : null;

            // Format date
            $tanggalFormatted = \Carbon\Carbon::parse($data->tanggal_surat)->locale('id')->isoFormat('dddd, D MMMM YYYY');
            $jamFormatted = str_replace(':', '.', $data->jam_surat);

            // Handle "An." prefix
            $anStaf = ($data->an_staf == 1 || $data->an_staf === true) ? 'An. ' : '';
            $anNakhoda = ($data->an_nakhoda == 1 || $data->an_nakhoda === true) ? 'An. ' : '';
            $anKkm = ($data->an_kkm == 1 || $data->an_kkm === true) ? 'An. ' : '';
            $anNakhodaTemp = ($data->an_nakhoda_temp == 1 || $data->an_nakhoda_temp === true) ? 'An. ' : '';
            $anKkmTemp = ($data->an_kkm_temp == 1 || $data->an_kkm_temp === true) ? 'An. ' : '';

            // Get data BA Pengembalian yang di-link
            $baPengembalian = null;
            $kapalTemp = null;
            if ($data->link_modul_temp) {
                $baPengembalian = BbmKapaltrans::where('nomor_surat', $data->link_modul_temp)->first();
            }

            // Get kapal temp data
            if ($data->kapal_code_temp) {
                $kapalTemp = MKapal::where('code_kapal', $data->kapal_code_temp)->first();
            }

            $nomorSuratPengembalian = $baPengembalian ? $baPengembalian->nomor_surat : '';
            $tanggalPengembalian = $baPengembalian ? \Carbon\Carbon::parse($baPengembalian->tanggal_surat)->locale('id')->isoFormat('D MMMM YYYY') : '';
            $namaKapalTemp = $kapalTemp ? $kapalTemp->nama_kapal : ($data->kapal_code_temp ?? '');

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

            // Content - sesuai project_ci
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td width="100%" align="center">
                            <font size="12"><b><u>BERITA ACARA PENERIMAAN PENGEMBALIAN PEMINJAMAN BBM</b></u></font>
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
                        <td width="50%" align="justify">' . $data->nama_nahkoda . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="20%" align="justify">Pangkat/Gol</td>
                        <td width="3%" align="justify">:</td>
                        <td width="50%" align="justify">' . $data->pangkat_nahkoda . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="20%" align="justify">Jabatan</td>
                        <td width="3%" align="justify">:</td>
                        <td width="50%" align="justify">' . $anNakhoda . 'Nakhoda KP ' . ($kapal ? $kapal->nama_kapal : '') . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="96%" align="justify">Selanjutnya disebut sebagai <b>Pihak I selaku penerima pengembalian peminjaman BBM</b></td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify">2.</td>
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
                        <td width="50%" align="justify">' . $anNakhodaTemp . 'Nakhoda KP ' . $namaKapalTemp . '</td>
                    </tr>
                    <tr>
                        <td width="4%" align="justify"></td>
                        <td width="96%" align="justify">Selanjutnya disebut sebagai <b>Pihak II selaku pemberi pengembalian peminjaman BBM</b></td>
                    </tr>
                </table>';

            // Narasi
            $html .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                    <td></td>
                    </tr>
                    <tr>
                    <td width="100%" align="justify">Pada hari ini ' . $tanggalFormatted . ' pukul ' . $jamFormatted . ' ' . $data->zona_waktu_surat . ' bertempat di ' . $data->lokasi_surat . ' berdasarkan Berita Acara Pengembalian Pinjaman BBM Nomor ' . $nomorSuratPengembalian . ' tanggal ' . $tanggalPengembalian . ', telah dilakukan pengembalian peminjaman BBM ' . $data->keterangan_jenis_bbm . ' dari PIHAK II ke PIHAK I sebanyak <b>' . number_format($data->volume_pengisian) . '</b> liter.
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
                        <td width="100%" align="justify">Demikian Berita Acara Penerimaan Pengembalian Peminjaman BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.
                        </td>
                    </tr>
                </table>';

            // Footer signatures - sesuai project_ci (Pihak I dan II terbalik posisinya)
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
                            <b>' . $anNakhodaTemp . ' Nakhoda KP. ' . $namaKapalTemp . '</b><br><br><br><br><br>
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
                            <b>' . $anKkmTemp . ' KKM KP. ' . $namaKapalTemp . '</b><br><br><br><br><br>
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
                            <b><u>' . $data->nama_staf_pagkalan . '</u></b><br>
                            <b>NIP. ' . $data->nip_staf . '</b>
                        </td>
                        <td width="30%" align="center"></td>
                    </tr>
                </table>';

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generate filename
            $filename = 'BA_Penerimaan_Pengembalian_Pinjaman_BBM_' . str_replace('/', '_', $data->nomor_surat) . '_' . date('Y-m-d_H-i-s');

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
            $ba = BbmKapaltrans::where('status_ba', 13)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 13)->findOrFail($id);

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
            $ba = BbmKapaltrans::where('status_ba', 13)->findOrFail($id);

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
