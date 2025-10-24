<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeDokumen extends Model
{
    protected $table = 'tipe_dokumen';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'kode_dokumen',
        'nama_dokumen',
        'deskripsi',
        'allowed_extensions',
        'max_size_kb',
        'is_active'
    ];

    protected $casts = [
        'allowed_extensions' => 'array',
        'max_size_kb' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relasi dengan foto upload
    public function fotoUploads()
    {
        return $this->hasMany(FotoUpload::class, 'tipe_dokumen_id', 'id');
    }

    // Scope untuk tipe dokumen yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk mencari berdasarkan kode
    public function scopeByKode($query, $kode)
    {
        return $query->where('kode_dokumen', $kode);
    }

    // Accessor untuk status aktif dalam teks
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    // Accessor untuk ukuran maksimal dalam format yang lebih mudah dibaca
    public function getMaxSizeFormattedAttribute()
    {
        $kb = $this->max_size_kb;
        $units = ['KB', 'MB', 'GB'];

        for ($i = 0; $kb > 1024 && $i < count($units) - 1; $i++) {
            $kb /= 1024;
        }

        return round($kb, 2) . ' ' . $units[$i];
    }

    // Method untuk mengecek apakah ekstensi file diizinkan
    public function isExtensionAllowed($extension)
    {
        return in_array(strtolower($extension), $this->allowed_extensions ?? []);
    }

    // Method untuk mengecek apakah ukuran file diizinkan
    public function isSizeAllowed($sizeInBytes)
    {
        $maxSizeInBytes = $this->max_size_kb * 1024;
        return $sizeInBytes <= $maxSizeInBytes;
    }
}
