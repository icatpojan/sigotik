<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Sigotik API",
 *     version="1.0.0",
 *     description="API untuk Sistem Manajemen BBM Kapal (Sigotik)",
 *     @OA\Contact(
 *         email="support@sigotik.com",
 *         name="Sigotik Support Team"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Development Server"
 * )
 *
 * @OA\Server(
 *     url="https://api.sigotik.com",
 *     description="Production Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Bearer token"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints for user authentication"
 * )
 *
 * @OA\Tag(
 *     name="BBM Management",
 *     description="API endpoints for BBM (Bahan Bakar Minyak) management"
 * )
 *
 * @OA\Tag(
 *     name="Master Data",
 *     description="API endpoints for master data management (UPT, Kapal, Users)"
 * )
 *
 * @OA\Tag(
 *     name="BA Sebelum Pelayaran",
 *     description="API endpoints for BA Sebelum Pelayaran management"
 * )
 *
 * @OA\Tag(
 *     name="BA Sesudah Pelayaran",
 *     description="API endpoints for BA Sesudah Pelayaran management"
 * )
 *
 * @OA\Tag(
 *     name="BA Penggunaan BBM",
 *     description="API endpoints for BA Penggunaan BBM management"
 * )
 *
 * @OA\Tag(
 *     name="BA Penerimaan BBM",
 *     description="API endpoints for BA Penerimaan BBM management"
 * )
 *
 * @OA\Tag(
 *     name="BA Penitipan BBM",
 *     description="API endpoints for BA Penitipan BBM management"
 * )
 *
 * @OA\Tag(
 *     name="BA Pengembalian BBM",
 *     description="API endpoints for BA Pengembalian BBM management"
 * )
 *
 * @OA\Tag(
 *     name="BA Peminjaman BBM",
 *     description="API endpoints for BA Peminjaman BBM management"
 * )
 *
 * @OA\Tag(
 *     name="BA Penerimaan Pinjaman BBM",
 *     description="API endpoints for BA Penerimaan Pinjaman BBM management"
 * )
 *
 * @OA\Tag(
 *     name="BA Pengembalian Pinjaman BBM",
 *     description="API endpoints for BA Pengembalian Pinjaman BBM management"
 * )
 *
 * @OA\Tag(
 *     name="BA Penerimaan Pengembalian Pinjaman BBM",
 *     description="API endpoints for BA Penerimaan Pengembalian Pinjaman BBM management"
 * )
 *
 * @OA\Tag(
 *     name="BA Pemberi Hibah BBM Kapal Pengawas",
 *     description="API endpoints for BA Pemberi Hibah BBM Kapal Pengawas management"
 * )
 *
 * @OA\Tag(
 *     name="BA Penerima Hibah BBM Kapal Pengawas",
 *     description="API endpoints for BA Penerima Hibah BBM Kapal Pengawas management"
 * )
 *
 * @OA\Tag(
 *     name="BA Pemberi Hibah BBM Dengan Instansi Lain",
 *     description="API endpoints for BA Pemberi Hibah BBM Dengan Instansi Lain management"
 * )
 *
 * @OA\Tag(
 *     name="BA Penerima Hibah BBM Dengan Instansi Lain",
 *     description="API endpoints for BA Penerima Hibah BBM Dengan Instansi Lain management"
 * )
 *
 * @OA\Tag(
 *     name="BA Penerimaan Hibah BBM",
 *     description="API endpoints for BA Penerimaan Hibah BBM management"
 * )
 *
 * @OA\Tag(
 *     name="BA Pemeriksaan Sarana Pengisian",
 *     description="API endpoints for BA Pemeriksaan Sarana Pengisian management"
 * )
 *
 * @OA\Tag(
 *     name="BA Akhir Bulan",
 *     description="API endpoints for BA Akhir Bulan management"
 * )
 *
 * @OA\Tag(
 *     name="Dashboard",
 *     description="API endpoints for dashboard and statistics"
 * )
 *
 * @OA\Tag(
 *     name="Search",
 *     description="API endpoints for global search functionality"
 * )
 */
class SwaggerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api",
     *     tags={"General"},
     *     summary="API Information",
     *     description="Get basic information about the Sigotik API",
     *     @OA\Response(
     *         response=200,
     *         description="API information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sigotik API v1.0"),
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(
     *                 property="endpoints",
     *                 type="object",
     *                 @OA\Property(property="authentication", type="string", example="/api/v1/auth/login"),
     *                 @OA\Property(property="bbm", type="string", example="/api/v1/bbm"),
     *                 @OA\Property(property="kapal", type="string", example="/api/v1/kapals"),
     *                 @OA\Property(property="upt", type="string", example="/api/v1/upts"),
     *                 @OA\Property(property="ba_modules", type="string", example="/api/v1/ba-*")
     *             )
     *         )
     *     )
     * )
     */
    public function apiInfo()
    {
        return response()->json([
            'message' => 'Sigotik API v1.0',
            'version' => '1.0.0',
            'endpoints' => [
                'authentication' => '/api/v1/auth/login',
                'bbm' => '/api/v1/bbm',
                'kapal' => '/api/v1/kapals',
                'upt' => '/api/v1/upts',
                'ba_modules' => '/api/v1/ba-*'
            ]
        ]);
    }
}
