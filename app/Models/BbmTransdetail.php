<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmTransdetail extends Model
{

    protected $table = 'bbm_transdetail';
    protected $primaryKey = 'bbm_transdetail_id';
    public $timestamps = false;

    protected $fillable = [
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
        'tanggalinput',
        'userid'
    ];

    protected $dates = [
        'tgl_invoice',
        'tanggalinput'
    ];
}
