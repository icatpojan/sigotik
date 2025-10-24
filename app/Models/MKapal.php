<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MKapal extends Model
{

    protected $table = 'm_kapal';
    protected $primaryKey = 'm_kapal_id';
    public $timestamps = false;

    protected $fillable = [
        'm_kapal_id',
        'nama_kapal',
        'code_kapal',
        'm_upt_code',
        'bobot',
        'panjang',
        'tinggi',
        'lebar',
        'main_engine',
        'jml_main_engine',
        'pk_main_engine',
        'aux_engine_utama',
        'jml_aux_engine_utama',
        'pk_aux_engine_utama',
        'gerak_engine',
        'aux_engine_emergency',
        'galangan_pembuat',
        'kapasitas_tangki',
        'jml_tangki',
        'tahun_buat',
        'jml_abk',
        'nama_nakoda',
        'nip_nakoda',
        'jabatan_nakoda',
        'pangkat_nakoda',
        'golongan_nakoda',
        'gambar_kapal',
        'lampiran_kapal',
        'nama_kkm',
        'nip_kkm',
        'jabatan_kkm',
        'pangkat_kkm',
        'golongan_kkm',
        'date_insert',
        'user_insert',
        'date_update',
        'user_update'
    ];

    protected $dates = [
        'date_insert',
        'date_update'
    ];

    // Relasi dengan UPT
    public function upt()
    {
        return $this->belongsTo(MUpt::class, 'm_upt_code', 'code');
    }

    // Relasi dengan transaksi BBM
    public function bbmKapaltrans()
    {
        return $this->hasMany(BbmKapaltrans::class, 'kapal_code', 'code_kapal');
    }

    // Relasi many-to-many dengan user
    public function users()
    {
        return $this->belongsToMany(ConfUser::class, 'sys_user_kapal', 'm_kapal_id', 'conf_user_id');
    }

    // Accessor untuk gambar_kapal agar mengembalikan URL lengkap
    public function getGambarKapalAttribute($value)
    {
        if ($value) {
            return url($value);
        }
        return null;
    }
}
