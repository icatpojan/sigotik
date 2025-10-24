<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoUpload extends Model
{
    protected $table = 'foto_uploads';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nomor_surat',
        'trans_id',
        'tipe_ba_id',
        'tipe_dokumen_id',
        'nama_file',
        'nama_file_stored',
        'path_file',
        'mime_type',
        'ukuran_file',
        'keterangan',
        'user_upload_id'
    ];

    protected $casts = [
        'ukuran_file' => 'integer',
        'trans_id' => 'integer',
        'tipe_ba_id' => 'integer',
        'tipe_dokumen_id' => 'integer',
        'user_upload_id' => 'integer'
    ];

    // Relasi dengan transaksi BBM
    public function bbmKapaltrans()
    {
        return $this->belongsTo(BbmKapaltrans::class, 'trans_id', 'trans_id');
    }

    // Relasi dengan tipe BA
    public function tipeBa()
    {
        return $this->belongsTo(TipeBa::class, 'tipe_ba_id', 'id');
    }

    // Relasi dengan tipe dokumen
    public function tipeDokumen()
    {
        return $this->belongsTo(TipeDokumen::class, 'tipe_dokumen_id', 'id');
    }

    // Relasi dengan user yang upload
    public function userUpload()
    {
        return $this->belongsTo(ConfUser::class, 'user_upload_id', 'conf_user_id');
    }

    // Accessor untuk URL lengkap file
    public function getUrlAttribute()
    {
        return url($this->path_file);
    }

    // Accessor untuk ukuran file yang sudah diformat
    public function getUkuranFileFormattedAttribute()
    {
        $bytes = $this->ukuran_file;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Scope untuk filter berdasarkan nomor surat
    public function scopeByNomorSurat($query, $nomorSurat)
    {
        return $query->where('nomor_surat', $nomorSurat);
    }

    // Scope untuk filter berdasarkan trans_id
    public function scopeByTransId($query, $transId)
    {
        return $query->where('trans_id', $transId);
    }

    // Scope untuk filter berdasarkan tipe BA
    public function scopeByTipeBa($query, $tipeBaId)
    {
        return $query->where('tipe_ba_id', $tipeBaId);
    }

    // Scope untuk filter berdasarkan tipe dokumen
    public function scopeByTipeDokumen($query, $tipeDokumenId)
    {
        return $query->where('tipe_dokumen_id', $tipeDokumenId);
    }

    // Scope untuk filter berdasarkan user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_upload_id', $userId);
    }
}
