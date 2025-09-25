<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfGroup extends Model
{

    protected $table = 'conf_group';
    protected $primaryKey = 'conf_group_id';
    public $timestamps = false;

    protected $fillable = [
        'group'
    ];

    // Relasi dengan user
    public function users()
    {
        return $this->hasMany(ConfUser::class, 'conf_group_id', 'conf_group_id');
    }

    // Relasi many-to-many dengan menu
    public function menus()
    {
        return $this->belongsToMany(StmMenuv2::class, 'conf_role_menu', 'conf_group_id', 'stm_menu_id');
    }
}
