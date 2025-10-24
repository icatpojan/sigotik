<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmAnggaranUpt extends Model
{
    protected $table = 'bbm_anggaran_upt';
    protected $primaryKey = 'anggaran_upt_id';
    public $incrementing = false; // Set to false karena auto increment tidak berfungsi
    public $timestamps = false; // Disable timestamps karena tabel tidak memiliki created_at dan updated_at

    protected $fillable = [
        'anggaran_upt_id',
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

    // Override boot method untuk generate ID otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->anggaran_upt_id)) {
                $model->anggaran_upt_id = static::getNextId();
            }
        });
    }

    // Method untuk mendapatkan ID berikutnya
    public static function getNextId()
    {
        $maxId = static::max('anggaran_upt_id');
        return $maxId ? $maxId + 1 : 1;
    }

    protected $casts = [
        'tanggal_trans' => 'date',
        'tanggal_input' => 'datetime',
        'tanggal_app' => 'datetime',
        'nominal' => 'decimal:2',
        'statusperubahan' => 'integer'
    ];

    public function upt()
    {
        return $this->belongsTo(MUpt::class, 'm_upt_code', 'code');
    }

    public function userInput()
    {
        return $this->belongsTo(ConfUser::class, 'user_input', 'username');
    }

    public function userApp()
    {
        return $this->belongsTo(ConfUser::class, 'user_app', 'username');
    }


    // Accessor untuk status dalam teks
    public function getStatusPerubahanTextAttribute()
    {
        $statusMap = [
            0 => 'Belum Disetujui',
            1 => 'Disetujui',
            2 => 'Dibatalkan'
        ];

        return $statusMap[$this->statusperubahan] ?? 'Unknown';
    }

    // Accessor untuk status badge class
    public function getStatusBadgeClassAttribute()
    {
        $classMap = [
            0 => 'bg-yellow-100 text-yellow-800',
            1 => 'bg-green-100 text-green-800',
            2 => 'bg-red-100 text-red-800'
        ];

        return $classMap[$this->statusperubahan] ?? 'bg-gray-100 text-gray-800';
    }
}
