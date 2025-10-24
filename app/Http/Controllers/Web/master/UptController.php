<?php

namespace App\Http\Controllers\Web\master;

use Illuminate\Http\Request;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class UptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('upts.index');
    }

    /**
     * Get UPTs data via AJAX
     */
    public function getUpts(Request $request)
    {
        $query = MUpt::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('kota', 'like', "%{$search}%")
                    ->orWhere('nama_petugas', 'like', "%{$search}%")
                    ->orWhere('nip_petugas', 'like', "%{$search}%");
            });
        }

        // Filter by zona waktu
        if ($request->has('zona_waktu') && $request->zona_waktu) {
            $query->where('zona_waktu_upt', $request->zona_waktu);
        }

        // Per page parameter
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $upts = $query->orderBy('nama')->paginate($perPage);

        return response()->json([
            'success' => true,
            'upts' => $upts->items(),
            'pagination' => [
                'current_page' => $upts->currentPage(),
                'last_page' => $upts->lastPage(),
                'per_page' => $upts->perPage(),
                'total' => $upts->total(),
                'from' => $upts->firstItem(),
                'to' => $upts->lastItem(),
                'has_more_pages' => $upts->hasMorePages(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:m_upt,code',
            'alamat1' => 'nullable|string|max:255',
            'alamat2' => 'nullable|string|max:255',
            'alamat3' => 'nullable|string|max:200',
            'kota' => 'nullable|string|max:100',
            'zona_waktu_upt' => 'required|in:WIB,WITA,WIT',
            'nama_petugas' => 'nullable|string|max:50',
            'nip_petugas' => 'nullable|string|max:50',
            'jabatan_petugas' => 'nullable|string|max:50',
            'pangkat_petugas' => 'nullable|string|max:30',
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
            $maxId = MUpt::max('m_upt_id') ?? 0;
            $newId = $maxId + 1;

            $upt = MUpt::create([
                'm_upt_id' => $newId,
                'nama' => $request->nama,
                'code' => $request->code,
                'alamat1' => $request->alamat1,
                'alamat2' => $request->alamat2,
                'alamat3' => $request->alamat3,
                'kota' => $request->kota,
                'zona_waktu_upt' => $request->zona_waktu_upt,
                'nama_petugas' => $request->nama_petugas,
                'nip_petugas' => $request->nip_petugas,
                'jabatan_petugas' => $request->jabatan_petugas,
                'pangkat_petugas' => $request->pangkat_petugas,
                'date_insert' => now(),
                'user_insert' => auth()->user()->conf_user_id ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'UPT berhasil dibuat',
                'upt' => $upt
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
    public function show(MUpt $upt)
    {
        return response()->json([
            'success' => true,
            'upt' => $upt
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MUpt $upt)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:m_upt,code,' . $upt->m_upt_id . ',m_upt_id',
            'alamat1' => 'nullable|string|max:255',
            'alamat2' => 'nullable|string|max:255',
            'alamat3' => 'nullable|string|max:200',
            'kota' => 'nullable|string|max:100',
            'zona_waktu_upt' => 'required|in:WIB,WITA,WIT',
            'nama_petugas' => 'nullable|string|max:50',
            'nip_petugas' => 'nullable|string|max:50',
            'jabatan_petugas' => 'nullable|string|max:50',
            'pangkat_petugas' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $upt->update([
                'nama' => $request->nama,
                'code' => $request->code,
                'alamat1' => $request->alamat1,
                'alamat2' => $request->alamat2,
                'alamat3' => $request->alamat3,
                'kota' => $request->kota,
                'zona_waktu_upt' => $request->zona_waktu_upt,
                'nama_petugas' => $request->nama_petugas,
                'nip_petugas' => $request->nip_petugas,
                'jabatan_petugas' => $request->jabatan_petugas,
                'pangkat_petugas' => $request->pangkat_petugas,
                'date_update' => now(),
                'user_update' => auth()->user()->conf_user_id ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'UPT berhasil diperbarui',
                'upt' => $upt
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
    public function destroy(MUpt $upt)
    {
        try {
            $upt->delete();

            return response()->json([
                'success' => true,
                'message' => 'UPT berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
