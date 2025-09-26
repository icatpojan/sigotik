<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StmMenuv2 extends Model
{

    protected $table = 'stm_menuv2';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_parentmenu',
        'level',
        'menu',
        'linka',
        'icon',
        'urutan'
    ];

    // Self-reference untuk parent menu
    public function parent()
    {
        return $this->belongsTo(StmMenuv2::class, 'id_parentmenu', 'id');
    }

    // Self-reference untuk child menu
    public function children()
    {
        return $this->hasMany(StmMenuv2::class, 'id_parentmenu', 'id');
    }

    // Relasi many-to-many dengan grup
    public function groups()
    {
        return $this->belongsToMany(ConfGroup::class, 'conf_role_menu', 'stm_menu_id', 'conf_group_id');
    }
}
