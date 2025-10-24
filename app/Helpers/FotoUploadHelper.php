<?php

namespace App\Helpers;

use App\Models\FotoUpload;
use App\Models\TipeDokumen;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FotoUploadHelper
{
    /**
     * Upload foto dengan validasi
     */
    public static function uploadFoto(
        UploadedFile $file,
        int $tipeBaId,
        int $tipeDokumenId,
        ?string $nomorSurat = null,
        ?int $transId = null,
        ?string $keterangan = null,
        ?int $userId = null
    ): array {
        try {
            $tipeDokumen = TipeDokumen::findOrFail($tipeDokumenId);
            $userId = $userId ?? auth()->id();

            // Validasi ekstensi file
            $extension = $file->getClientOriginalExtension();
            if (!$tipeDokumen->isExtensionAllowed($extension)) {
                throw new \Exception('Ekstensi file tidak diizinkan. Ekstensi yang diizinkan: ' . implode(', ', $tipeDokumen->allowed_extensions));
            }

            // Validasi ukuran file
            if (!$tipeDokumen->isSizeAllowed($file->getSize())) {
                throw new \Exception('Ukuran file terlalu besar. Maksimal: ' . $tipeDokumen->max_size_formatted);
            }

            // Generate nama file unik
            $originalName = $file->getClientOriginalName();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $storedName = Str::slug($fileName) . '_' . time() . '_' . Str::random(8) . '.' . $extension;

            // Path penyimpanan
            $path = 'uploads/foto/' . date('Y/m/d');
            $fullPath = $path . '/' . $storedName;

            // Simpan file
            $file->storeAs($path, $storedName, 'public');

            // Simpan ke database
            $fotoUpload = FotoUpload::create([
                'nomor_surat' => $nomorSurat,
                'trans_id' => $transId,
                'tipe_ba_id' => $tipeBaId,
                'tipe_dokumen_id' => $tipeDokumenId,
                'nama_file' => $originalName,
                'nama_file_stored' => $storedName,
                'path_file' => 'storage/' . $fullPath,
                'mime_type' => $file->getMimeType(),
                'ukuran_file' => $file->getSize(),
                'keterangan' => $keterangan,
                'user_upload_id' => $userId
            ]);

            return [
                'success' => true,
                'data' => $fotoUpload,
                'url' => $fotoUpload->url
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Upload multiple foto
     */
    public static function uploadMultipleFoto(
        array $files,
        int $tipeBaId,
        int $tipeDokumenId,
        ?string $nomorSurat = null,
        ?int $transId = null,
        ?string $keterangan = null,
        ?int $userId = null
    ): array {
        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($files as $file) {
            $result = self::uploadFoto($file, $tipeBaId, $tipeDokumenId, $nomorSurat, $transId, $keterangan, $userId);

            if ($result['success']) {
                $successCount++;
                $results['success'][] = $result['data'];
            } else {
                $errorCount++;
                $results['errors'][] = [
                    'file' => $file->getClientOriginalName(),
                    'message' => $result['message']
                ];
            }
        }

        return [
            'success' => $successCount > 0,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'results' => $results
        ];
    }

    /**
     * Hapus foto
     */
    public static function deleteFoto(int $fotoId): array
    {
        try {
            $fotoUpload = FotoUpload::findOrFail($fotoId);

            // Hapus file dari storage
            $filePath = str_replace('storage/', '', $fotoUpload->path_file);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Hapus dari database
            $fotoUpload->delete();

            return [
                'success' => true,
                'message' => 'Foto berhasil dihapus'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Dapatkan foto berdasarkan filter
     */
    public static function getFotoByFilter(array $filters = []): array
    {
        try {
            $query = FotoUpload::with(['tipeBa', 'tipeDokumen', 'userUpload']);

            // Filter berdasarkan nomor surat
            if (isset($filters['nomor_surat']) && $filters['nomor_surat']) {
                $query->where('nomor_surat', 'like', '%' . $filters['nomor_surat'] . '%');
            }

            // Filter berdasarkan trans_id
            if (isset($filters['trans_id']) && $filters['trans_id']) {
                $query->where('trans_id', $filters['trans_id']);
            }

            // Filter berdasarkan tipe BA
            if (isset($filters['tipe_ba_id']) && $filters['tipe_ba_id']) {
                $query->where('tipe_ba_id', $filters['tipe_ba_id']);
            }

            // Filter berdasarkan tipe dokumen
            if (isset($filters['tipe_dokumen_id']) && $filters['tipe_dokumen_id']) {
                $query->where('tipe_dokumen_id', $filters['tipe_dokumen_id']);
            }

            // Filter berdasarkan user
            if (isset($filters['user_id']) && $filters['user_id']) {
                $query->where('user_upload_id', $filters['user_id']);
            }

            // Filter berdasarkan tanggal
            if (isset($filters['tanggal_awal']) && $filters['tanggal_awal']) {
                $query->whereDate('created_at', '>=', $filters['tanggal_awal']);
            }

            if (isset($filters['tanggal_akhir']) && $filters['tanggal_akhir']) {
                $query->whereDate('created_at', '<=', $filters['tanggal_akhir']);
            }

            $perPage = $filters['per_page'] ?? 15;
            $fotos = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return [
                'success' => true,
                'data' => $fotos->items(),
                'pagination' => [
                    'current_page' => $fotos->currentPage(),
                    'last_page' => $fotos->lastPage(),
                    'per_page' => $fotos->perPage(),
                    'total' => $fotos->total(),
                    'from' => $fotos->firstItem(),
                    'to' => $fotos->lastItem()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil daftar foto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Dapatkan statistik foto
     */
    public static function getFotoStats(): array
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

            return [
                'success' => true,
                'data' => [
                    'total_foto' => $totalFoto,
                    'total_ukuran' => self::formatBytes($totalUkuran),
                    'foto_hari_ini' => $fotoHariIni,
                    'foto_bulan_ini' => $fotoBulanIni,
                    'statistik_tipe_ba' => $statistikTipeBa,
                    'statistik_tipe_dokumen' => $statistikTipeDokumen
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil statistik foto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format bytes ke format yang lebih mudah dibaca
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Cleanup foto yang tidak terpakai
     */
    public static function cleanupUnusedFotos(): array
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
                $result = self::deleteFoto($foto->id);
                if ($result['success']) {
                    $deletedCount++;
                } else {
                    $errors[] = $result['message'];
                }
            }

            return [
                'success' => true,
                'message' => "Cleanup selesai. {$deletedCount} foto dihapus.",
                'deleted_count' => $deletedCount,
                'errors' => $errors
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal melakukan cleanup: ' . $e->getMessage()
            ];
        }
    }
}
