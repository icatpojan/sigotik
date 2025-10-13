<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmAnggaran extends Model
{
    protected $table = 'bbm_anggaran';
    protected $primaryKey = 'anggaran_id';
    public $timestamps = false;

    protected $fillable = [
        'anggaran_id',
        'periode',
        'm_upt_code',
        'anggaran',
        'perubahan_ke',
        'keterangan',
        'statusanggaran',
        'user_input',
        'tanggal_input',
        'user_app',
        'tanggal_app'
    ];

    protected $dates = [
        'tanggal_input',
        'tanggal_app'
    ];

    protected $casts = [
        'anggaran' => 'decimal:2',
        'statusanggaran' => 'integer',
        'perubahan_ke' => 'integer',
        'periode' => 'integer'
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

    // Scope untuk filter berdasarkan status anggaran
    public function scopeByStatus($query, $status)
    {
        return $query->where('statusanggaran', $status);
    }

    // Scope untuk filter berdasarkan periode
    public function scopeByPeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    // Scope untuk filter berdasarkan perubahan ke
    public function scopeByPerubahanKe($query, $perubahanKe)
    {
        return $query->where('perubahan_ke', $perubahanKe);
    }

    // Method untuk mendapatkan status anggaran dalam teks
    public function getStatusAnggaranTextAttribute()
    {
        $statusMap = [
            0 => 'Belum Disetujui',
            1 => 'Sudah Disetujui'
        ];

        return $statusMap[$this->statusanggaran] ?? 'Unknown';
    }

    // Method untuk format anggaran dengan currency
    public function getAnggaranFormattedAttribute()
    {
        return 'Rp. ' . number_format($this->anggaran, 0, ',', '.');
    }
}
