<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmTagihan extends Model
{

    protected $table = 'bbm_tagihan';
    protected $primaryKey = 'tagihan_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tagihan_id',
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
        'tanggal_batal',
        'tagihanke',
        'no_spt',
        'file_sppd'
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

    // Relasi dengan user yang membatalkan
    public function userBatal()
    {
        return $this->belongsTo(ConfUser::class, 'user_batal', 'username');
    }

    // Method untuk mendapatkan status tagihan dalam teks
    public function getStatusTagihanTextAttribute()
    {
        $statusMap = [
            0 => 'Input',
            1 => 'Disetujui',
            2 => 'Dibatalkan'
        ];

        return $statusMap[$this->statustagihan] ?? 'Tidak Diketahui';
    }
}
