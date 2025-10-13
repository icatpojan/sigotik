<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Master Data",
 *     description="API endpoints for master data management (UPT, Kapal, Users)"
 * )
 */
class UptController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/upts",
     *     tags={"Master Data"},
     *     summary="Get UPT list",
     *     description="Retrieve list of all UPTs with optional filtering",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for nama or code",
     *         required=false,
     *         @OA\Schema(type="string", example="Jakarta")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="UPT list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="m_upt_id", type="integer", example=1),
     *                     @OA\Property(property="nama", type="string", example="UPT Jakarta"),
     *                     @OA\Property(property="code", type="string", example="UPT001"),
     *                     @OA\Property(property="alamat1", type="string", example="Jl. Sudirman No. 1"),
     *                     @OA\Property(property="alamat2", type="string", example="Jakarta Pusat"),
     *                     @OA\Property(property="alamat3", type="string", example="DKI Jakarta"),
     *                     @OA\Property(property="kota", type="string", example="Jakarta"),
     *                     @OA\Property(property="zona_waktu_upt", type="string", example="WIB"),
     *                     @OA\Property(property="nama_petugas", type="string", example="Admin UPT"),
     *                     @OA\Property(property="nip_petugas", type="string", example="123456789"),
     *                     @OA\Property(property="jabatan_petugas", type="string", example="Kepala UPT"),
     *                     @OA\Property(property="pangkat_petugas", type="string", example="IV/a")
     *                 )
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
        $query = MUpt::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('kota', 'like', "%{$search}%");
            });
        }

        $upts = $query->orderBy('nama', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $upts
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/upts/{id}",
     *     tags={"Master Data"},
     *     summary="Get UPT detail",
     *     description="Retrieve detailed information of a specific UPT",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UPT ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="UPT detail retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="UPT not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="UPT tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $upt = MUpt::find($id);

        if (!$upt) {
            return response()->json([
                'success' => false,
                'message' => 'UPT tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $upt
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/upts",
     *     tags={"Master Data"},
     *     summary="Create new UPT",
     *     description="Create a new UPT record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama","code"},
     *             @OA\Property(property="nama", type="string", example="UPT Jakarta"),
     *             @OA\Property(property="code", type="string", example="UPT001"),
     *             @OA\Property(property="alamat1", type="string", example="Jl. Sudirman No. 1"),
     *             @OA\Property(property="alamat2", type="string", example="Jakarta Pusat"),
     *             @OA\Property(property="alamat3", type="string", example="DKI Jakarta"),
     *             @OA\Property(property="kota", type="string", example="Jakarta"),
     *             @OA\Property(property="zona_waktu_upt", type="string", example="WIB", enum={"WIB", "WITA", "WIT"}),
     *             @OA\Property(property="nama_petugas", type="string", example="Admin UPT"),
     *             @OA\Property(property="nip_petugas", type="string", example="123456789"),
     *             @OA\Property(property="jabatan_petugas", type="string", example="Kepala UPT"),
     *             @OA\Property(property="pangkat_petugas", type="string", example="IV/a")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="UPT created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="UPT berhasil dibuat"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validasi gagal"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
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
            'zona_waktu_upt' => 'nullable|in:WIB,WITA,WIT',
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
                'zona_waktu_upt' => $request->zona_waktu_upt ?? 'WIB',
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
                'data' => $upt
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/upts/{id}",
     *     tags={"Master Data"},
     *     summary="Update UPT",
     *     description="Update an existing UPT record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UPT ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama", type="string", example="UPT Jakarta Updated"),
     *             @OA\Property(property="alamat1", type="string", example="Jl. Sudirman No. 2")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="UPT updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="UPT berhasil diperbarui"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="UPT not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="UPT tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $upt = MUpt::find($id);

        if (!$upt) {
            return response()->json([
                'success' => false,
                'message' => 'UPT tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:10|unique:m_upt,code,' . $id . ',m_upt_id',
            'alamat1' => 'nullable|string|max:255',
            'alamat2' => 'nullable|string|max:255',
            'alamat3' => 'nullable|string|max:200',
            'kota' => 'nullable|string|max:100',
            'zona_waktu_upt' => 'nullable|in:WIB,WITA,WIT',
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
            $upt->update(array_merge($request->all(), [
                'date_update' => now(),
                'user_update' => auth()->user()->conf_user_id ?? 1,
            ]));

            return response()->json([
                'success' => true,
                'message' => 'UPT berhasil diperbarui',
                'data' => $upt
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/upts/{id}",
     *     tags={"Master Data"},
     *     summary="Delete UPT",
     *     description="Delete a UPT record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UPT ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="UPT deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="UPT berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="UPT not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="UPT tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $upt = MUpt::find($id);

        if (!$upt) {
            return response()->json([
                'success' => false,
                'message' => 'UPT tidak ditemukan'
            ], 404);
        }

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
