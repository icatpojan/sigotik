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

    // Relasi dengan UPT
    public function upt()
    {
        return $this->belongsTo(MUpt::class, 'm_upt_code', 'code');
    }
}
