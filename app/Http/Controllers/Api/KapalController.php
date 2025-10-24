<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Master Data",
 *     description="API endpoints for master data management (UPT, Kapal, Users)"
 * )
 */
class KapalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/kapals",
     *     tags={"Master Data"},
     *     summary="Get Kapal list",
     *     description="Retrieve list of all kapals with optional filtering",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for nama kapal or code kapal",
     *         required=false,
     *         @OA\Schema(type="string", example="Patroli")
     *     ),
     *     @OA\Parameter(
     *         name="m_upt_code",
     *         in="query",
     *         description="Filter by UPT code",
     *         required=false,
     *         @OA\Schema(type="string", example="UPT001")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kapal list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="m_kapal_id", type="integer", example=1),
     *                     @OA\Property(property="nama_kapal", type="string", example="Kapal Patroli 001"),
     *                     @OA\Property(property="code_kapal", type="string", example="KAP001"),
     *                     @OA\Property(property="m_upt_code", type="string", example="UPT001"),
     *                     @OA\Property(property="bobot", type="number", format="float", example=150.50),
     *                     @OA\Property(property="panjang", type="number", format="float", example=25.5),
     *                     @OA\Property(property="tinggi", type="number", format="float", example=8.2),
     *                     @OA\Property(property="lebar", type="number", format="float", example=6.5),
     *                     @OA\Property(property="main_engine", type="string", example="Caterpillar C18"),
     *                     @OA\Property(property="jml_main_engine", type="integer", example=2),
     *                     @OA\Property(property="pk_main_engine", type="string", example="500 HP"),
     *                     @OA\Property(property="kapasitas_tangki", type="string", example="2000 Liter"),
     *                     @OA\Property(property="jml_tangki", type="integer", example=4),
     *                     @OA\Property(property="tahun_buat", type="integer", example=2020),
     *                     @OA\Property(property="jml_abk", type="integer", example=12),
     *                     @OA\Property(property="nama_nakoda", type="string", example="John Doe"),
     *                     @OA\Property(property="nip_nakoda", type="string", example="123456789"),
     *                     @OA\Property(property="nama_kkm", type="string", example="Jane Smith"),
     *                     @OA\Property(property="nip_kkm", type="string", example="987654321"),
     *                     @OA\Property(
     *                         property="upt",
     *                         type="object",
     *                         @OA\Property(property="nama", type="string", example="UPT Jakarta"),
     *                         @OA\Property(property="alamat1", type="string", example="Jl. Sudirman No. 1")
     *                     )
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
        $user = $request->user();
        $query = MKapal::with('upt');

        // Role-based filtering
        if ($user->conf_group_id != 1) { // Jika bukan admin
            // Ambil kapal yang terkait dengan user melalui sys_user_kapal
            $userKapalIds = DB::table('sys_user_kapal')
                ->where('conf_user_id', $user->conf_user_id)
                ->pluck('m_kapal_id')
                ->toArray();

            if (!empty($userKapalIds)) {
                $query->whereIn('m_kapal_id', $userKapalIds);
            } else {
                // Jika user tidak memiliki kapal, return empty
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kapal', 'like', "%{$search}%")
                    ->orWhere('code_kapal', 'like', "%{$search}%");
            });
        }

        // Filter by UPT
        if ($request->has('m_upt_code') && $request->m_upt_code) {
            $query->where('m_upt_code', $request->m_upt_code);
        }

        if ($request->m_kapal_id) {
            $query->where('m_kapal_id', $request->m_kapal_id);
        }

        $kapals = $query->orderBy('nama_kapal', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $kapals
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/kapals/{id}",
     *     tags={"Master Data"},
     *     summary="Get Kapal detail",
     *     description="Retrieve detailed information of a specific kapal",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Kapal ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kapal detail retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kapal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kapal tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $query = MKapal::with('upt');

        // Role-based filtering
        if ($user->conf_group_id != 1) { // Jika bukan admin
            // Ambil kapal yang terkait dengan user melalui sys_user_kapal
            $userKapalIds = DB::table('sys_user_kapal')
                ->where('conf_user_id', $user->conf_user_id)
                ->pluck('m_kapal_id')
                ->toArray();

            if (!empty($userKapalIds)) {
                $query->whereIn('m_kapal_id', $userKapalIds);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Kapal tidak ditemukan'
                ], 404);
            }
        }

        $kapal = $query->find($id);

        if (!$kapal) {
            return response()->json([
                'success' => false,
                'message' => 'Kapal tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kapal
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/kapals",
     *     tags={"Master Data"},
     *     summary="Create new Kapal",
     *     description="Create a new kapal record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_kapal","code_kapal","m_upt_code"},
     *             @OA\Property(property="nama_kapal", type="string", example="Kapal Patroli 002"),
     *             @OA\Property(property="code_kapal", type="string", example="KAP002"),
     *             @OA\Property(property="m_upt_code", type="string", example="UPT001"),
     *             @OA\Property(property="bobot", type="number", format="float", example=150.50),
     *             @OA\Property(property="panjang", type="number", format="float", example=25.5),
     *             @OA\Property(property="tinggi", type="number", format="float", example=8.2),
     *             @OA\Property(property="lebar", type="number", format="float", example=6.5),
     *             @OA\Property(property="main_engine", type="string", example="Caterpillar C18"),
     *             @OA\Property(property="jml_main_engine", type="integer", example=2),
     *             @OA\Property(property="pk_main_engine", type="string", example="500 HP"),
     *             @OA\Property(property="kapasitas_tangki", type="string", example="2000 Liter"),
     *             @OA\Property(property="jml_tangki", type="integer", example=4),
     *             @OA\Property(property="tahun_buat", type="integer", example=2020),
     *             @OA\Property(property="jml_abk", type="integer", example=12),
     *             @OA\Property(property="nama_nakoda", type="string", example="John Doe"),
     *             @OA\Property(property="nip_nakoda", type="string", example="123456789"),
     *             @OA\Property(property="nama_kkm", type="string", example="Jane Smith"),
     *             @OA\Property(property="nip_kkm", type="string", example="987654321")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Kapal created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kapal berhasil dibuat"),
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
            'nama_kapal' => 'required|string|max:150',
            'code_kapal' => 'required|string|max:30|unique:m_kapal,code_kapal',
            'm_upt_code' => 'required|string|max:10|exists:m_upt,code',
            'bobot' => 'nullable|numeric|min:0',
            'panjang' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'main_engine' => 'nullable|string|max:100',
            'jml_main_engine' => 'nullable|integer|min:0',
            'pk_main_engine' => 'nullable|string|max:100',
            'kapasitas_tangki' => 'nullable|string|max:100',
            'jml_tangki' => 'nullable|integer|min:0',
            'tahun_buat' => 'nullable|integer|min:1900|max:' . date('Y'),
            'jml_abk' => 'nullable|integer|min:0',
            'nama_nakoda' => 'nullable|string|max:100',
            'nip_nakoda' => 'nullable|string|max:50',
            'nama_kkm' => 'nullable|string|max:100',
            'nip_kkm' => 'nullable|string|max:50',
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

            $kapal = MKapal::create([
                'm_kapal_id' => $newId,
                'nama_kapal' => $request->nama_kapal,
                'code_kapal' => $request->code_kapal,
                'm_upt_code' => $request->m_upt_code,
                'bobot' => $request->bobot,
                'panjang' => $request->panjang,
                'tinggi' => $request->tinggi,
                'lebar' => $request->lebar,
                'main_engine' => $request->main_engine ?? '0',
                'jml_main_engine' => $request->jml_main_engine ?? 0,
                'pk_main_engine' => $request->pk_main_engine,
                'kapasitas_tangki' => $request->kapasitas_tangki,
                'jml_tangki' => $request->jml_tangki,
                'tahun_buat' => $request->tahun_buat,
                'jml_abk' => $request->jml_abk,
                'nama_nakoda' => $request->nama_nakoda,
                'nip_nakoda' => $request->nip_nakoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'date_insert' => now(),
                'user_insert' => auth()->user()->conf_user_id ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kapal berhasil dibuat',
                'data' => $kapal->load('upt')
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
     *     path="/api/v1/kapals/{id}",
     *     tags={"Master Data"},
     *     summary="Update Kapal",
     *     description="Update an existing kapal record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Kapal ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_kapal", type="string", example="Kapal Patroli 002 Updated"),
     *             @OA\Property(property="bobot", type="number", format="float", example=160.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kapal updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kapal berhasil diperbarui"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kapal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kapal tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $kapal = MKapal::find($id);

        if (!$kapal) {
            return response()->json([
                'success' => false,
                'message' => 'Kapal tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_kapal' => 'sometimes|required|string|max:150',
            'code_kapal' => 'sometimes|required|string|max:30|unique:m_kapal,code_kapal,' . $id . ',m_kapal_id',
            'm_upt_code' => 'sometimes|required|string|max:10|exists:m_upt,code',
            'bobot' => 'nullable|numeric|min:0',
            'panjang' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $kapal->update(array_merge($request->all(), [
                'date_update' => now(),
                'user_update' => auth()->user()->conf_user_id ?? 1,
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Kapal berhasil diperbarui',
                'data' => $kapal->load('upt')
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
     *     path="/api/v1/kapals/{id}",
     *     tags={"Master Data"},
     *     summary="Delete Kapal",
     *     description="Delete a kapal record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Kapal ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kapal deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kapal berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kapal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kapal tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $kapal = MKapal::find($id);

        if (!$kapal) {
            return response()->json([
                'success' => false,
                'message' => 'Kapal tidak ditemukan'
            ], 404);
        }

        try {
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
