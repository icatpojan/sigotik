<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmAnggaran extends Model
{

    protected $table = 'bbm_anggaran';
    protected $primaryKey = 'anggaran_id';
    public $timestamps = false;

    protected $fillable = [
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

    // Relasi dengan UPT
    public function upt()
    {
        return $this->belongsTo(MUpt::class, 'm_upt_code', 'code');
    }
}
