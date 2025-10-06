<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbmKapaltrans extends Model
{
    protected $table = 'bbm_kapaltrans';
    protected $primaryKey = 'trans_id';
    public $timestamps = true;

    protected $fillable = [
        'trans_id',
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
        'penyedia',
        'kesimpulan',
        'jabatan_staf_pangkalan', // Tambahkan field yang hilang
        'nama_staf_pangkalan', // Tambahkan field yang hilang
        'nip_staf', // Tambahkan field yang hilang
        'an_staf', // Tambahkan field yang hilang
        'peruntukan', // Field untuk peruntukan BBM
        'link_modul_ba', // Field untuk link BA
        'penyedia_penitip', // Field untuk penyedia penitip
        'nama_penitip', // Field untuk nama penitip
        'jabatan_penitip', // Field untuk jabatan penitip
        'alamat_penitip', // Field untuk alamat penitip
        'alamat_penyedia_penitip', // Field untuk alamat penyedia penitip
        'penggunaan', // Field untuk jumlah penitipan BBM
        'volume_sebelum', // Field untuk volume tangki pengukuran
        'volume_pemakaian', // Field untuk volume tangki saat ini
        'nama_nahkoda',
        'nip_nahkoda',
        'jabatan_nahkoda',
        'pangkat_nahkoda',
        'file_upload',
        'golongan_nahkoda',
        'nama_staf_pagkalan',
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
        'sebab_temp', // Field untuk alasan peminjaman BBM
        'nomer_persetujuan', // Field untuk nomor persetujuan
        'tgl_persetujuan', // Field untuk tanggal persetujuan
        'm_persetujuan_id', // Field untuk ID jenis persetujuan
        'status_temp', // Field untuk status sementara
        'link_modul_temp', // Field untuk link modul temp
        'user_input',
        'tanggal_input',
        'user_app',
        'tanggal_app',
        'status_trans',
        'penyedia', // Field untuk penyedia BBM
        'no_so', // Field untuk nomor Sales Order
        'penyedia_penitip', // Field untuk penyedia penitip
        'nama_penitip', // Field untuk nama penitip
        'jabatan_penitip', // Field untuk jabatan penitip
        'alamat_penitip', // Field untuk alamat penitip
        'penggunaan', // Field untuk jumlah penitipan BBM
        'volume_sebelum', // Field untuk volume tangki pengukuran
        'tanggal_sebelum', // Field untuk tanggal sebelum
        'an_staf', // Field untuk checkbox An. staf
        'an_nakhoda', // Field untuk checkbox An. nakhoda
        'an_kkm', // Field untuk checkbox An. kkm
        'status_upload', // Field untuk status upload dokumen
        'file_upload' // Field untuk file upload dokumen
    ];

    protected $dates = [
        'tanggal_surat',
        'tanggal_sebelum',
        'tanggal_pengisian',
        'tanggal_input',
        'tanggal_app'
    ];

    protected $casts = [
        'volume_sisa' => 'decimal:2',
        'volume_sebelum' => 'decimal:2',
        'volume_pengisian' => 'decimal:2',
        'volume_pemakaian' => 'decimal:2',
        'status_ba' => 'integer',
        'status_segel' => 'integer',
        'status_flowmeter' => 'integer',
        'an_nakhoda' => 'integer',
        'an_kkm' => 'integer',
        'an_nakhoda_temp' => 'integer',
        'an_kkm_temp' => 'integer',
        'status_trans' => 'integer',
        'an_staf' => 'integer',
        'penggunaan' => 'decimal:2',
        'status_upload' => 'integer'
    ];

    // Relasi dengan kapal
    public function kapal()
    {
        return $this->belongsTo(MKapal::class, 'kapal_code', 'code_kapal');
    }

    // Relasi dengan user yang input
    public function userInput()
    {
        return $this->belongsTo(ConfUser::class, 'user_input', 'conf_user_id');
    }

    // Relasi dengan user yang approve
    public function userApp()
    {
        return $this->belongsTo(ConfUser::class, 'user_app', 'conf_user_id');
    }

    // Relasi dengan detail transportasi (untuk BA Penerimaan BBM)
    public function transdetails()
    {
        return $this->hasMany(BbmTransdetail::class, 'nomor_surat', 'nomor_surat');
    }

    // Scope untuk filter berdasarkan status BA
    public function scopeByStatusBa($query, $status)
    {
        return $query->where('status_ba', $status);
    }

    // Scope untuk filter berdasarkan status transaksi
    public function scopeByStatusTrans($query, $status)
    {
        return $query->where('status_trans', $status);
    }

    // Scope untuk filter berdasarkan kapal
    public function scopeByKapal($query, $kapalCode)
    {
        return $query->where('kapal_code', $kapalCode);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
    }

    // Method untuk mendapatkan status BA dalam teks
    public function getStatusBaTextAttribute()
    {
        $statusMap = [
            0 => 'BA Default',
            1 => 'BA Akhir Bulan',
            2 => 'BA Sebelum Pengisian',
            3 => 'BA Penggunaan BBM',
            4 => 'BA Pemeriksaan Sarana Pengisian',
            5 => 'BA Penerimaan BBM',
            6 => 'BA Sebelum Pelayaran',
            7 => 'BA Sesudah Pelayaran',
            8 => 'BA Penitipan BBM',
            9 => 'BA Pengembalian BBM',
            10 => 'BA Peminjaman BBM',
            11 => 'BA Penerimaan Pinjaman BBM',
            12 => 'BA Pemberi Hibah BBM Kapal Pengawas',
            13 => 'BA Penerima Hibah BBM Kapal Pengawas',
            14 => 'BA Penerima Hibah BBM Instansi Lain',
            15 => 'BA Akhir Bulan'
        ];

        return $statusMap[$this->status_ba] ?? 'Unknown';
    }

    // Method untuk mendapatkan status transaksi dalam teks
    public function getStatusTransTextAttribute()
    {
        $statusMap = [
            0 => 'Input',
            1 => 'Approval',
            2 => 'Batal'
        ];

        return $statusMap[$this->status_trans] ?? 'Unknown';
    }

    // Method untuk mendapatkan zona waktu dalam teks
    public function getZonaWaktuTextAttribute()
    {
        $zonaMap = [
            'WIB' => 'Waktu Indonesia Barat',
            'WITA' => 'Waktu Indonesia Tengah',
            'WIT' => 'Waktu Indonesia Timur'
        ];

        return $zonaMap[$this->zona_waktu_surat] ?? $this->zona_waktu_surat;
    }
}
