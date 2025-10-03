<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'status'
    ];

    // Relasi dengan berita
    public function portNews()
    {
        return $this->hasMany(PortNews::class, 'kategori_id', 'id');
    }
}
