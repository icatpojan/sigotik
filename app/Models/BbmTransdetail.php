<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmTransdetail extends Model
{
    protected $table = 'bbm_transdetail';
    protected $primaryKey = 'bbm_transdetail_id';
    public $timestamps = false;

    protected $fillable = [
        'bbm_transdetail_id',
        'nomor_surat',
        'transportasi',
        'no_so',
        'no_do',
        'volume_isi',
        'keterangan',
        'no_invoice',
        'tgl_invoice',
        'harga_total',
        'status_bayar',
        'no_tagihan',
        'foto_do',
        'foto_segel',
        'foto_volume',
        'tanggalinput',
        'userid'
    ];

    protected $dates = [
        'tanggalinput'
    ];

    protected $casts = [
        'volume_isi' => 'decimal:2',
        'harga_total' => 'decimal:2',
        'status_bayar' => 'integer',
        'tgl_invoice' => 'date'
    ];

    // Relasi dengan BbmKapaltrans
    public function bbmKapaltrans()
    {
        return $this->belongsTo(BbmKapaltrans::class, 'nomor_surat', 'nomor_surat');
    }

    // Scope untuk filter berdasarkan nomor surat
    public function scopeByNomorSurat($query, $nomorSurat)
    {
        return $query->where('nomor_surat', $nomorSurat);
    }

    // Scope untuk filter berdasarkan status pembayaran
    public function scopeByStatusBayar($query, $status)
    {
        return $query->where('status_bayar', $status);
    }

    // Method untuk mendapatkan total volume per nomor surat
    public static function getTotalVolume($nomorSurat)
    {
        return self::where('nomor_surat', $nomorSurat)->sum('volume_isi');
    }

    // Method untuk cek apakah sudah ada pembayaran
    public static function hasPayment($nomorSurat)
    {
        return self::where('nomor_surat', $nomorSurat)
            ->where('status_bayar', 1)
            ->exists();
    }
}
