<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FotoUpload;
use App\Models\TipeBa;
use App\Models\TipeDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FotoUploadController extends Controller
{
    /**
     * Upload foto
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|image|max:10240', // Max 10MB
            'nomor_surat' => 'nullable|string|max:255',
            'trans_id' => 'nullable|integer|exists:bbm_kapaltrans,trans_id',
            'tipe_ba_id' => 'required|integer|exists:tipe_ba,id',
            'tipe_dokumen_id' => 'required|integer|exists:tipe_dokumen,id',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $tipeDokumen = TipeDokumen::find($request->tipe_dokumen_id);

            // Validasi ekstensi file
            $extension = $file->getClientOriginalExtension();
            if (!$tipeDokumen->isExtensionAllowed($extension)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ekstensi file tidak diizinkan. Ekstensi yang diizinkan: ' . implode(', ', $tipeDokumen->allowed_extensions)
                ], 422);
            }

            // Validasi ukuran file
            if (!$tipeDokumen->isSizeAllowed($file->getSize())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal: ' . $tipeDokumen->max_size_formatted
                ], 422);
            }

            // Generate nama file unik
            $originalName = $file->getClientOriginalName();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $storedName = Str::slug($fileName) . '_' . time() . '_' . Str::random(8) . '.' . $extension;

            // Path penyimpanan
            $path = 'uploads/foto/' . date('Y/m/d');
            $fullPath = $path . '/' . $storedName;

            // Simpan file
            $file->storeAs($path, $storedName, 'public');

            // Simpan ke database
            $fotoUpload = FotoUpload::create([
                'nomor_surat' => $request->nomor_surat,
                'trans_id' => $request->trans_id,
                'tipe_ba_id' => $request->tipe_ba_id,
                'tipe_dokumen_id' => $request->tipe_dokumen_id,
                'nama_file' => $originalName,
                'nama_file_stored' => $storedName,
                'path_file' => 'storage/' . $fullPath,
                'mime_type' => $file->getMimeType(),
                'ukuran_file' => $file->getSize(),
                'keterangan' => $request->keterangan,
                'user_upload_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diupload',
                'data' => [
                    'id' => $fotoUpload->id,
                    'url' => $fotoUpload->url,
                    'nama_file' => $fotoUpload->nama_file,
                    'ukuran_file' => $fotoUpload->ukuran_file_formatted,
                    'tipe_ba' => $fotoUpload->tipeBa->nama_ba,
                    'tipe_dokumen' => $fotoUpload->tipeDokumen->nama_dokumen,
                    'uploaded_at' => $fotoUpload->created_at->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload multiple foto
     */
    public function uploadMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|min:1|max:10', // Max 10 files
            'files.*' => 'required|file|image|max:10240', // Max 10MB per file
            'nomor_surat' => 'nullable|string|max:255',
            'trans_id' => 'nullable|integer|exists:bbm_kapaltrans,trans_id',
            'tipe_ba_id' => 'required|integer|exists:tipe_ba,id',
            'tipe_dokumen_id' => 'required|integer|exists:tipe_dokumen,id',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $files = $request->file('files');
            $tipeDokumen = TipeDokumen::find($request->tipe_dokumen_id);
            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($files as $index => $file) {
                // Validasi ekstensi file
                $extension = $file->getClientOriginalExtension();
                if (!$tipeDokumen->isExtensionAllowed($extension)) {
                    $results['errors'][] = [
                        'file_index' => $index,
                        'file_name' => $file->getClientOriginalName(),
                        'message' => 'Ekstensi file tidak diizinkan. Ekstensi yang diizinkan: ' . implode(', ', $tipeDokumen->allowed_extensions)
                    ];
                    $errorCount++;
                    continue;
                }

                // Validasi ukuran file
                if (!$tipeDokumen->isSizeAllowed($file->getSize())) {
                    $results['errors'][] = [
                        'file_index' => $index,
                        'file_name' => $file->getClientOriginalName(),
                        'message' => 'Ukuran file terlalu besar. Maksimal: ' . $tipeDokumen->max_size_formatted
                    ];
                    $errorCount++;
                    continue;
                }

                // Generate nama file unik
                $originalName = $file->getClientOriginalName();
                $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $storedName = Str::slug($fileName) . '_' . time() . '_' . Str::random(8) . '_' . $index . '.' . $extension;

                // Path penyimpanan
                $path = 'uploads/foto/' . date('Y/m/d');
                $fullPath = $path . '/' . $storedName;

                // Simpan file
                $file->storeAs($path, $storedName, 'public');

                // Simpan ke database
                $fotoUpload = FotoUpload::create([
                    'nomor_surat' => $request->nomor_surat,
                    'trans_id' => $request->trans_id,
                    'tipe_ba_id' => $request->tipe_ba_id,
                    'tipe_dokumen_id' => $request->tipe_dokumen_id,
                    'nama_file' => $originalName,
                    'nama_file_stored' => $storedName,
                    'path_file' => 'storage/' . $fullPath,
                    'mime_type' => $file->getMimeType(),
                    'ukuran_file' => $file->getSize(),
                    'keterangan' => $request->keterangan,
                    'user_upload_id' => auth()->id()
                ]);

                $results['success'][] = [
                    'file_index' => $index,
                    'file_name' => $originalName,
                    'id' => $fotoUpload->id,
                    'url' => $fotoUpload->url,
                    'ukuran_file' => $fotoUpload->ukuran_file_formatted
                ];
                $successCount++;
            }

            return response()->json([
                'success' => $successCount > 0,
                'message' => "Upload selesai. {$successCount} foto berhasil, {$errorCount} foto gagal",
                'data' => [
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'total_files' => count($files),
                    'results' => $results
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan foto berdasarkan ID
     */
    public function show($id)
    {
        try {
            $fotoUpload = FotoUpload::with(['tipeBa', 'tipeDokumen', 'userUpload', 'bbmKapaltrans'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $fotoUpload->id,
                    'nomor_surat' => $fotoUpload->nomor_surat,
                    'trans_id' => $fotoUpload->trans_id,
                    'url' => $fotoUpload->url,
                    'nama_file' => $fotoUpload->nama_file,
                    'ukuran_file' => $fotoUpload->ukuran_file_formatted,
                    'mime_type' => $fotoUpload->mime_type,
                    'keterangan' => $fotoUpload->keterangan,
                    'tipe_ba' => [
                        'id' => $fotoUpload->tipeBa->id,
                        'kode' => $fotoUpload->tipeBa->kode_ba,
                        'nama' => $fotoUpload->tipeBa->nama_ba
                    ],
                    'tipe_dokumen' => [
                        'id' => $fotoUpload->tipeDokumen->id,
                        'kode' => $fotoUpload->tipeDokumen->kode_dokumen,
                        'nama' => $fotoUpload->tipeDokumen->nama_dokumen
                    ],
                    'uploader' => [
                        'id' => $fotoUpload->userUpload->conf_user_id,
                        'nama' => $fotoUpload->userUpload->nama
                    ],
                    'bbm_transaksi' => $fotoUpload->bbmKapaltrans ? [
                        'trans_id' => $fotoUpload->bbmKapaltrans->trans_id,
                        'nomor_surat' => $fotoUpload->bbmKapaltrans->nomor_surat,
                        'kapal_code' => $fotoUpload->bbmKapaltrans->kapal_code,
                        'tanggal_surat' => $fotoUpload->bbmKapaltrans->tanggal_surat
                    ] : null,
                    'uploaded_at' => $fotoUpload->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $fotoUpload->updated_at->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Foto tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Daftar foto dengan filter
     */
    public function index(Request $request)
    {
        try {
            $query = FotoUpload::with(['tipeBa', 'tipeDokumen', 'userUpload', 'bbmKapaltrans']);

            // Filter berdasarkan nomor surat
            if ($request->has('nomor_surat') && $request->nomor_surat) {
                $query->where('nomor_surat', 'like', '%' . $request->nomor_surat . '%');
            }

            // Filter berdasarkan trans_id
            if ($request->has('trans_id') && $request->trans_id) {
                $query->where('trans_id', $request->trans_id);
            }

            // Filter berdasarkan tipe BA
            if ($request->has('tipe_ba_id') && $request->tipe_ba_id) {
                $query->where('tipe_ba_id', $request->tipe_ba_id);
            }

            // Filter berdasarkan tipe dokumen
            if ($request->has('tipe_dokumen_id') && $request->tipe_dokumen_id) {
                $query->where('tipe_dokumen_id', $request->tipe_dokumen_id);
            }

            // Filter berdasarkan user
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_upload_id', $request->user_id);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $fotos = $query->orderBy('created_at', 'desc')->paginate($perPage);

            $data = $fotos->map(function ($foto) {
                return [
                    'id' => $foto->id,
                    'nomor_surat' => $foto->nomor_surat,
                    'trans_id' => $foto->trans_id,
                    'url' => $foto->url,
                    'nama_file' => $foto->nama_file,
                    'ukuran_file' => $foto->ukuran_file_formatted,
                    'tipe_ba' => $foto->tipeBa->nama_ba,
                    'tipe_dokumen' => $foto->tipeDokumen->nama_dokumen,
                    'uploader' => $foto->userUpload->nama,
                    'bbm_transaksi' => $foto->bbmKapaltrans ? [
                        'trans_id' => $foto->bbmKapaltrans->trans_id,
                        'nomor_surat' => $foto->bbmKapaltrans->nomor_surat,
                        'kapal_code' => $foto->bbmKapaltrans->kapal_code
                    ] : null,
                    'uploaded_at' => $foto->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $fotos->currentPage(),
                    'last_page' => $fotos->lastPage(),
                    'per_page' => $fotos->perPage(),
                    'total' => $fotos->total(),
                    'from' => $fotos->firstItem(),
                    'to' => $fotos->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus foto
     */
    public function destroy($id)
    {
        try {
            $fotoUpload = FotoUpload::findOrFail($id);

            // Hapus file dari storage
            $filePath = str_replace('storage/', '', $fotoUpload->path_file);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Hapus dari database
            $fotoUpload->delete();

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Daftar tipe BA
     */
    public function getTipeBa()
    {
        try {
            $tipeBa = TipeBa::active()->orderBy('nama_ba')->get();

            return response()->json([
                'success' => true,
                'data' => $tipeBa->map(function ($tipe) {
                    return [
                        'id' => $tipe->id,
                        'kode' => $tipe->kode_ba,
                        'nama' => $tipe->nama_ba,
                        'deskripsi' => $tipe->deskripsi
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar tipe BA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Daftar tipe dokumen
     */
    public function getTipeDokumen()
    {
        try {
            $tipeDokumen = TipeDokumen::active()->orderBy('nama_dokumen')->get();

            return response()->json([
                'success' => true,
                'data' => $tipeDokumen->map(function ($tipe) {
                    return [
                        'id' => $tipe->id,
                        'kode' => $tipe->kode_dokumen,
                        'nama' => $tipe->nama_dokumen,
                        'deskripsi' => $tipe->deskripsi,
                        'allowed_extensions' => $tipe->allowed_extensions,
                        'max_size' => $tipe->max_size_formatted
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar tipe dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dapatkan foto berdasarkan trans_id
     */
    public function getByTransId($transId)
    {
        try {
            $fotos = FotoUpload::with(['tipeBa', 'tipeDokumen', 'userUpload', 'bbmKapaltrans'])
                ->where('trans_id', $transId)
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $fotos->map(function ($foto) {
                return [
                    'id' => $foto->id,
                    'nomor_surat' => $foto->nomor_surat,
                    'trans_id' => $foto->trans_id,
                    'url' => $foto->url,
                    'nama_file' => $foto->nama_file,
                    'ukuran_file' => $foto->ukuran_file_formatted,
                    'tipe_ba' => $foto->tipeBa->nama_ba,
                    'tipe_dokumen' => $foto->tipeDokumen->nama_dokumen,
                    'uploader' => $foto->userUpload->nama,
                    'keterangan' => $foto->keterangan,
                    'uploaded_at' => $foto->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $fotos->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil foto berdasarkan trans_id: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dapatkan statistik foto
     */
    public function getStatistics()
    {
        try {
            $totalFoto = FotoUpload::count();
            $totalUkuran = FotoUpload::sum('ukuran_file');
            $fotoHariIni = FotoUpload::whereDate('created_at', today())->count();
            $fotoBulanIni = FotoUpload::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // Statistik per tipe BA
            $statistikTipeBa = FotoUpload::with('tipeBa')
                ->selectRaw('tipe_ba_id, COUNT(*) as total')
                ->groupBy('tipe_ba_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'tipe_ba' => $item->tipeBa->nama_ba,
                        'total' => $item->total
                    ];
                });

            // Statistik per tipe dokumen
            $statistikTipeDokumen = FotoUpload::with('tipeDokumen')
                ->selectRaw('tipe_dokumen_id, COUNT(*) as total')
                ->groupBy('tipe_dokumen_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'tipe_dokumen' => $item->tipeDokumen->nama_dokumen,
                        'total' => $item->total
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_foto' => $totalFoto,
                    'total_ukuran' => $this->formatBytes($totalUkuran),
                    'foto_hari_ini' => $fotoHariIni,
                    'foto_bulan_ini' => $fotoBulanIni,
                    'statistik_tipe_ba' => $statistikTipeBa,
                    'statistik_tipe_dokumen' => $statistikTipeDokumen
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup foto yang tidak terpakai
     */
    public function cleanup()
    {
        try {
            // Cari foto yang tidak memiliki nomor surat atau nomor surat tidak ada di bbm_kapaltrans
            $unusedFotos = FotoUpload::where(function ($query) {
                $query->whereNull('nomor_surat')
                    ->orWhereNotExists(function ($subQuery) {
                        $subQuery->select(\DB::raw(1))
                            ->from('bbm_kapaltrans')
                            ->whereColumn('bbm_kapaltrans.nomor_surat', 'foto_uploads.nomor_surat');
                    });
            })->get();

            $deletedCount = 0;
            $errors = [];

            foreach ($unusedFotos as $foto) {
                try {
                    // Hapus file dari storage
                    $filePath = str_replace('storage/', '', $foto->path_file);
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }

                    // Hapus dari database
                    $foto->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal menghapus foto ID {$foto->id}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Cleanup selesai. {$deletedCount} foto dihapus.",
                'data' => [
                    'deleted_count' => $deletedCount,
                    'total_unused' => $unusedFotos->count(),
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan cleanup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes ke format yang lebih mudah dibaca
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
