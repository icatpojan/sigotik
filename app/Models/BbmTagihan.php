<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmTagihan extends Model
{

    protected $table = 'bbm_tagihan';
    protected $primaryKey = 'tagihan_id';
    public $timestamps = false;

    protected $fillable = [
        'm_upt_code',
        'no_tagihan',
        'tanggal_invoice',
        'no_invoice',
        'penyedia',
        'quantity',
        'harga',
        'hargaperliter',
        'ppn',
        'total',
        'statustagihan',
        'tanggal_sppd',
        'file',
        'user_input',
        'tanggal_input',
        'user_app',
        'tanggal_app',
        'user_batal',
        'tanggal_batal'
    ];

    protected $dates = [
        'tanggal_invoice',
        'tanggal_sppd',
        'tanggal_input',
        'tanggal_app',
        'tanggal_batal'
    ];

    // Relasi dengan UPT
    public function upt()
    {
        return $this->belongsTo(MUpt::class, 'm_upt_code', 'code');
    }
}
