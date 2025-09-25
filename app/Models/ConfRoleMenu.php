<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfRoleMenu extends Model
{

    protected $table = 'conf_role_menu';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'conf_group_id',
        'stm_menu_id'
    ];

    // Relasi dengan grup
    public function group()
    {
        return $this->belongsTo(ConfGroup::class, 'conf_group_id', 'conf_group_id');
    }

    // Relasi dengan menu
    public function menu()
    {
        return $this->belongsTo(StmMenuv2::class, 'stm_menu_id', 'id');
    }
}
