<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmKapaltrans extends Model
{

    protected $table = 'bbm_kapaltrans';
    protected $primaryKey = 'trans_id';
    public $timestamps = false;

    protected $fillable = [
        'kapal_code',
        'nomor_surat',
        'tanggal_surat',
        'jam_surat',
        'zona_waktu_surat',
        'lokasi_surat',
        'volume_sisa',
        'volume_sebelum',
        'tanggal_sebelum',
        'volume_pengisian',
        'tanggal_pengisian',
        'volume_pemakaian',
        'nomor_nota',
        'keterangan_jenis_bbm',
        'status_ba',
        'jenis_tranport',
        'status_segel',
        'gambar_segel',
        'status_flowmeter',
        'gambar_flowmeter',
        'nama_nahkoda',
        'nip_nahkoda',
        'jabatan_nahkoda',
        'pangkat_nahkoda',
        'golongan_nahkoda',
        'nama_kkm',
        'nip_kkm',
        'jabatan_kkm',
        'pangkat_kkm',
        'golongan_kkm',
        'nama_an',
        'nip_an',
        'jabatan_an',
        'pangkat_an',
        'golongan_an',
        'an_nakhoda',
        'an_kkm',
        'kapal_code_temp',
        'pangkat_nahkoda',
        'nama_nahkoda_temp',
        'nip_nahkoda_temp',
        'jabatan_nahkoda_temp',
        'pangkat_nahkoda_temp',
        'golongan_nahkoda_temp',
        'nama_kkm_temp',
        'nip_kkm_temp',
        'jabatan_kkm_temp',
        'pangkat_kkm_temp',
        'golongan_kkm_temp',
        'nama_an_temp',
        'nip_an_temp',
        'jabatan_an_temp',
        'pangkat_an_temp',
        'golongan_an_temp',
        'an_nakhoda_temp',
        'an_kkm_temp',
        'user_input',
        'tanggal_input',
        'user_app',
        'tanggal_app',
        'status_trans'
    ];

    protected $dates = [
        'tanggal_surat',
        'tanggal_sebelum',
        'tanggal_pengisian',
        'tanggal_input',
        'tanggal_app'
    ];

    // Relasi dengan kapal
    public function kapal()
    {
        return $this->belongsTo(MKapal::class, 'kapal_code', 'code_kapal');
    }
}
