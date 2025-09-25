<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MUpt extends Model
{

    protected $table = 'm_upt';
    protected $primaryKey = 'm_upt_id';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'code',
        'alamat1',
        'alamat2',
        'alamat3',
        'kota',
        'zona_waktu_upt',
        'nama_petugas',
        'nip_petugas',
        'jabatan_petugas',
        'pangkat_petugas',
        'date_insert',
        'user_insert',
        'date_update',
        'user_update'
    ];

    protected $dates = [
        'date_insert',
        'date_update'
    ];

    // Relasi dengan kapal
    public function kapals()
    {
        return $this->hasMany(MKapal::class, 'm_upt_code', 'code');
    }

    // Relasi dengan user
    public function users()
    {
        return $this->hasMany(ConfUser::class, 'm_upt_code', 'code');
    }

    // Relasi dengan anggaran BBM
    public function bbmAnggarans()
    {
        return $this->hasMany(BbmAnggaran::class, 'm_upt_code', 'code');
    }

    // Relasi dengan anggaran UPT
    public function bbmAnggaranUpts()
    {
        return $this->hasMany(BbmAnggaranUpt::class, 'm_upt_code', 'code');
    }

    // Relasi dengan tagihan BBM
    public function bbmTagihans()
    {
        return $this->hasMany(BbmTagihan::class, 'm_upt_code', 'code');
    }
}
