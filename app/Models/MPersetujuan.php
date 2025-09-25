<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPersetujuan extends Model
{

    protected $table = 'm_persetujuan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'deskripsi_persetujuan'
    ];
}
