<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortNews extends Model
{

    protected $table = 'port_news';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'img',
        'news_title',
        'news',
        'kategori_id',
        'author',
        'date_create',
        'post'
    ];

    protected $dates = [
        'date_create'
    ];

    // Relasi dengan kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id', 'id');
    }

    // Cast kategori_id ke string untuk kompatibilitas dengan enum
    protected $casts = [
        'kategori_id' => 'string',
        'post' => 'string',
        'date_create' => 'datetime',
    ];

    // Accessor untuk mengkonversi blob ke string
    public function getNewsAttribute($value)
    {
        if (is_resource($value)) {
            return stream_get_contents($value);
        }
        return $value;
    }
}
