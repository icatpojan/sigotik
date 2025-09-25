<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysUserKapal extends Model
{

    protected $table = 'sys_user_kapal';
    protected $primaryKey = 'sys_user_kapal_id';
    public $timestamps = false;

    protected $fillable = [
        'conf_user_id',
        'm_kapal_id'
    ];

    // Relasi dengan user
    public function user()
    {
        return $this->belongsTo(ConfUser::class, 'conf_user_id', 'conf_user_id');
    }

    // Relasi dengan kapal
    public function kapal()
    {
        return $this->belongsTo(MKapal::class, 'm_kapal_id', 'm_kapal_id');
    }
}
