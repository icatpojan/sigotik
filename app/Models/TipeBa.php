<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeBa extends Model
{
    protected $table = 'tipe_ba';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'kode_ba',
        'nama_ba',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relasi dengan foto upload
    public function fotoUploads()
    {
        return $this->hasMany(FotoUpload::class, 'tipe_ba_id', 'id');
    }

    // Scope untuk tipe BA yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk mencari berdasarkan kode
    public function scopeByKode($query, $kode)
    {
        return $query->where('kode_ba', $kode);
    }

    // Accessor untuk status aktif dalam teks
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }
}
