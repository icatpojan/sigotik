<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmAnggaranUpt extends Model
{
    protected $table = 'bbm_anggaran_upt';
    protected $primaryKey = 'anggaran_upt_id';
    public $timestamps = false;

    protected $fillable = [
        'tanggal_trans',
        'm_upt_code',
        'nominal',
        'nomor_surat',
        'keterangan',
        'statusperubahan',
        'user_input',
        'tanggal_input',
        'user_app',
        'tanggal_app'
    ];

    protected $dates = [
        'tanggal_trans',
        'tanggal_input',
        'tanggal_app'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'statusperubahan' => 'integer'
    ];

    // Relasi dengan UPT
    public function upt()
    {
        return $this->belongsTo(MUpt::class, 'm_upt_code', 'code');
    }

    // Relasi dengan user yang input
    public function userInput()
    {
        return $this->belongsTo(ConfUser::class, 'user_input', 'username');
    }

    // Relasi dengan user yang approve
    public function userApp()
    {
        return $this->belongsTo(ConfUser::class, 'user_app', 'username');
    }

    // Scope untuk filter berdasarkan status perubahan
    public function scopeByStatus($query, $status)
    {
        return $query->where('statusperubahan', $status);
    }

    // Scope untuk filter berdasarkan UPT
    public function scopeByUpt($query, $uptCode)
    {
        return $query->where('m_upt_code', $uptCode);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_trans', [$startDate, $endDate]);
    }

    // Method untuk mendapatkan status perubahan dalam teks
    public function getStatusPerubahanTextAttribute()
    {
        $statusMap = [
            0 => 'Belum Disetujui',
            1 => 'Sudah Disetujui',
            2 => 'Dibatalkan'
        ];

        return $statusMap[$this->statusperubahan] ?? 'Unknown';
    }

    // Method untuk format nominal dengan currency
    public function getNominalFormattedAttribute()
    {
        return 'Rp. ' . number_format($this->nominal, 0, ',', '.');
    }
}
