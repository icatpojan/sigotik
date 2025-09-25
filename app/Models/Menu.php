<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    protected $table = 'menus';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'url',
        'parent_id',
        'status',
        'position',
        'type'
    ];

    // Self-reference untuk parent menu
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id', 'id');
    }

    // Self-reference untuk child menu
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }
}
