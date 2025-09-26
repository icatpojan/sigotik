<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;

class KapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $upts = MUpt::all();
        return view('kapals.index', compact('upts'));
    }

    /**
     * Get Kapals data via AJAX
     */
    public function getKapals(Request $request)
    {
        $query = MKapal::with(['upt']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kapal', 'like', "%{$search}%")
                    ->orWhere('code_kapal', 'like', "%{$search}%")
                    ->orWhere('nama_nakoda', 'like', "%{$search}%")
                    ->orWhere('nama_kkm', 'like', "%{$search}%")
                    ->orWhereHas('upt', function ($uptQuery) use ($search) {
                        $uptQuery->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by UPT
        if ($request->has('upt') && $request->upt) {
            $query->where('m_upt_code', $request->upt);
        }

        // Filter by tahun buat
        if ($request->has('tahun_buat') && $request->tahun_buat) {
            $query->where('tahun_buat', $request->tahun_buat);
        }

        // Per page parameter
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $kapals = $query->orderBy('nama_kapal')->paginate($perPage);

        return response()->json([
            'success' => true,
            'kapals' => $kapals->items(),
            'pagination' => [
                'current_page' => $kapals->currentPage(),
                'last_page' => $kapals->lastPage(),
                'per_page' => $kapals->perPage(),
                'total' => $kapals->total(),
                'from' => $kapals->firstItem(),
                'to' => $kapals->lastItem(),
                'has_more_pages' => $kapals->hasMorePages(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kapal' => 'required|string|max:150',
            'code_kapal' => 'nullable|string|max:30|unique:m_kapal,code_kapal',
            'm_upt_code' => 'nullable|exists:m_upt,code',
            'bobot' => 'nullable|numeric',
            'panjang' => 'nullable|numeric',
            'tinggi' => 'nullable|numeric',
            'lebar' => 'nullable|numeric',
            'main_engine' => 'nullable|string|max:100',
            'jml_main_engine' => 'nullable|integer',
            'pk_main_engine' => 'nullable|string|max:100',
            'aux_engine_utama' => 'nullable|string|max:100',
            'jml_aux_engine_utama' => 'nullable|integer',
            'pk_aux_engine_utama' => 'nullable|string|max:100',
            'gerak_engine' => 'nullable|string|max:100',
            'aux_engine_emergency' => 'nullable|string|max:100',
            'galangan_pembuat' => 'nullable|string|max:100',
            'kapasitas_tangki' => 'nullable|string|max:100',
            'jml_tangki' => 'nullable|integer',
            'tahun_buat' => 'nullable|integer',
            'jml_abk' => 'nullable|integer',
            'nama_nakoda' => 'nullable|string|max:100',
            'nip_nakoda' => 'nullable|string|max:50',
            'jabatan_nakoda' => 'nullable|string|max:50',
            'pangkat_nakoda' => 'nullable|string|max:50',
            'golongan_nakoda' => 'nullable|string|max:50',
            'nama_kkm' => 'nullable|string|max:100',
            'nip_kkm' => 'nullable|string|max:50',
            'jabatan_kkm' => 'nullable|string|max:50',
            'pangkat_kkm' => 'nullable|string|max:50',
            'golongan_kkm' => 'nullable|string|max:50',
            'gambar_kapal' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'lampiran_kapal' => 'nullable|file|mimes:pdf|max:5120',
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
            $maxId = MKapal::max('m_kapal_id') ?? 0;
            $newId = $maxId + 1;

            $data = [
                'm_kapal_id' => $newId,
                'nama_kapal' => $request->nama_kapal,
                'code_kapal' => $request->code_kapal,
                'm_upt_code' => $request->m_upt_code,
                'bobot' => $request->bobot,
                'panjang' => $request->panjang,
                'tinggi' => $request->tinggi,
                'lebar' => $request->lebar,
                'main_engine' => $request->main_engine,
                'jml_main_engine' => $request->jml_main_engine,
                'pk_main_engine' => $request->pk_main_engine,
                'aux_engine_utama' => $request->aux_engine_utama,
                'jml_aux_engine_utama' => $request->jml_aux_engine_utama,
                'pk_aux_engine_utama' => $request->pk_aux_engine_utama,
                'gerak_engine' => $request->gerak_engine,
                'aux_engine_emergency' => $request->aux_engine_emergency,
                'galangan_pembuat' => $request->galangan_pembuat,
                'kapasitas_tangki' => $request->kapasitas_tangki,
                'jml_tangki' => $request->jml_tangki,
                'tahun_buat' => $request->tahun_buat,
                'jml_abk' => $request->jml_abk,
                'nama_nakoda' => $request->nama_nakoda,
                'nip_nakoda' => $request->nip_nakoda,
                'jabatan_nakoda' => $request->jabatan_nakoda,
                'pangkat_nakoda' => $request->pangkat_nakoda,
                'golongan_nakoda' => $request->golongan_nakoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'jabatan_kkm' => $request->jabatan_kkm,
                'pangkat_kkm' => $request->pangkat_kkm,
                'golongan_kkm' => $request->golongan_kkm,
                'date_insert' => now(),
                'user_insert' => auth()->user()->conf_user_id ?? 1,
            ];

            // Handle file uploads
            if ($request->hasFile('gambar_kapal')) {
                $gambarFile = $request->file('gambar_kapal');
                $gambarName = 'gambar_' . $newId . '_' . time() . '.' . $gambarFile->getClientOriginalExtension();
                $gambarFile->move(public_path('upload/kapals/images'), $gambarName);
                $data['gambar_kapal'] = 'upload/kapals/images/' . $gambarName;
            }

            if ($request->hasFile('lampiran_kapal')) {
                $lampiranFile = $request->file('lampiran_kapal');
                $lampiranName = 'lampiran_' . $newId . '_' . time() . '.' . $lampiranFile->getClientOriginalExtension();
                $lampiranFile->move(public_path('upload/kapals/documents'), $lampiranName);
                $data['lampiran_kapal'] = 'upload/kapals/documents/' . $lampiranName;
            }

            $kapal = MKapal::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Kapal berhasil dibuat',
                'kapal' => $kapal->load(['upt'])
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
    public function show(MKapal $kapal)
    {
        return response()->json([
            'success' => true,
            'kapal' => $kapal->load(['upt'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MKapal $kapal)
    {
        $validator = Validator::make($request->all(), [
            'nama_kapal' => 'required|string|max:150',
            'code_kapal' => 'nullable|string|max:30|unique:m_kapal,code_kapal,' . $kapal->m_kapal_id . ',m_kapal_id',
            'm_upt_code' => 'nullable|exists:m_upt,code',
            'bobot' => 'nullable|numeric',
            'panjang' => 'nullable|numeric',
            'tinggi' => 'nullable|numeric',
            'lebar' => 'nullable|numeric',
            'main_engine' => 'nullable|string|max:100',
            'jml_main_engine' => 'nullable|integer',
            'pk_main_engine' => 'nullable|string|max:100',
            'aux_engine_utama' => 'nullable|string|max:100',
            'jml_aux_engine_utama' => 'nullable|integer',
            'pk_aux_engine_utama' => 'nullable|string|max:100',
            'gerak_engine' => 'nullable|string|max:100',
            'aux_engine_emergency' => 'nullable|string|max:100',
            'galangan_pembuat' => 'nullable|string|max:100',
            'kapasitas_tangki' => 'nullable|string|max:100',
            'jml_tangki' => 'nullable|integer',
            'tahun_buat' => 'nullable|integer',
            'jml_abk' => 'nullable|integer',
            'nama_nakoda' => 'nullable|string|max:100',
            'nip_nakoda' => 'nullable|string|max:50',
            'jabatan_nakoda' => 'nullable|string|max:50',
            'pangkat_nakoda' => 'nullable|string|max:50',
            'golongan_nakoda' => 'nullable|string|max:50',
            'nama_kkm' => 'nullable|string|max:100',
            'nip_kkm' => 'nullable|string|max:50',
            'jabatan_kkm' => 'nullable|string|max:50',
            'pangkat_kkm' => 'nullable|string|max:50',
            'golongan_kkm' => 'nullable|string|max:50',
            'gambar_kapal' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'lampiran_kapal' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'nama_kapal' => $request->nama_kapal,
                'code_kapal' => $request->code_kapal,
                'm_upt_code' => $request->m_upt_code,
                'bobot' => $request->bobot,
                'panjang' => $request->panjang,
                'tinggi' => $request->tinggi,
                'lebar' => $request->lebar,
                'main_engine' => $request->main_engine,
                'jml_main_engine' => $request->jml_main_engine,
                'pk_main_engine' => $request->pk_main_engine,
                'aux_engine_utama' => $request->aux_engine_utama,
                'jml_aux_engine_utama' => $request->jml_aux_engine_utama,
                'pk_aux_engine_utama' => $request->pk_aux_engine_utama,
                'gerak_engine' => $request->gerak_engine,
                'aux_engine_emergency' => $request->aux_engine_emergency,
                'galangan_pembuat' => $request->galangan_pembuat,
                'kapasitas_tangki' => $request->kapasitas_tangki,
                'jml_tangki' => $request->jml_tangki,
                'tahun_buat' => $request->tahun_buat,
                'jml_abk' => $request->jml_abk,
                'nama_nakoda' => $request->nama_nakoda,
                'nip_nakoda' => $request->nip_nakoda,
                'jabatan_nakoda' => $request->jabatan_nakoda,
                'pangkat_nakoda' => $request->pangkat_nakoda,
                'golongan_nakoda' => $request->golongan_nakoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'jabatan_kkm' => $request->jabatan_kkm,
                'pangkat_kkm' => $request->pangkat_kkm,
                'golongan_kkm' => $request->golongan_kkm,
                'date_update' => now(),
                'user_update' => auth()->user()->conf_user_id ?? 1,
            ];

            // Handle file uploads
            if ($request->hasFile('gambar_kapal')) {
                // Delete old file if exists
                if ($kapal->gambar_kapal && file_exists(public_path($kapal->gambar_kapal))) {
                    unlink(public_path($kapal->gambar_kapal));
                }
                $gambarFile = $request->file('gambar_kapal');
                $gambarName = 'gambar_' . $kapal->m_kapal_id . '_' . time() . '.' . $gambarFile->getClientOriginalExtension();
                $gambarFile->move(public_path('upload/kapals/images'), $gambarName);
                $updateData['gambar_kapal'] = 'upload/kapals/images/' . $gambarName;
            }

            if ($request->hasFile('lampiran_kapal')) {
                // Delete old file if exists
                if ($kapal->lampiran_kapal && file_exists(public_path($kapal->lampiran_kapal))) {
                    unlink(public_path($kapal->lampiran_kapal));
                }
                $lampiranFile = $request->file('lampiran_kapal');
                $lampiranName = 'lampiran_' . $kapal->m_kapal_id . '_' . time() . '.' . $lampiranFile->getClientOriginalExtension();
                $lampiranFile->move(public_path('upload/kapals/documents'), $lampiranName);
                $updateData['lampiran_kapal'] = 'upload/kapals/documents/' . $lampiranName;
            }

            $kapal->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Kapal berhasil diperbarui',
                'kapal' => $kapal->load(['upt'])
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
    public function destroy(MKapal $kapal)
    {
        try {
            // Delete associated files
            if ($kapal->gambar_kapal && file_exists(public_path($kapal->gambar_kapal))) {
                unlink(public_path($kapal->gambar_kapal));
            }
            if ($kapal->lampiran_kapal && file_exists(public_path($kapal->lampiran_kapal))) {
                unlink(public_path($kapal->lampiran_kapal));
            }

            $kapal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kapal berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
