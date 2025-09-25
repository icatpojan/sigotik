<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortNews extends Model
{

    protected $table = 'port_news';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
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
}
