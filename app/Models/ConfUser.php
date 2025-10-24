<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class ConfUser extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'conf_user';
    protected $primaryKey = 'conf_user_id';
    public $timestamps = false;

    protected $fillable = [
        'conf_user_id',
        'username',
        'password',
        'm_upt_code',
        'conf_group_id',
        'email',
        'is_active',
        'nama_lengkap',
        'nip',
        'golongan',
        'ttd',
        'date_insert',
        'user_insert',
        'date_update',
        'user_update',
        'remember_token'
    ];

    protected $dates = [
        'date_insert',
        'date_update'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'conf_user_id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->conf_user_id;
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }


    // Relasi dengan UPT
    public function upt()
    {
        return $this->belongsTo(MUpt::class, 'm_upt_code', 'code');
    }

    // Relasi dengan grup
    public function group()
    {
        return $this->belongsTo(ConfGroup::class, 'conf_group_id', 'conf_group_id');
    }

    // Relasi many-to-many dengan kapal
    public function kapals()
    {
        return $this->belongsToMany(MKapal::class, 'sys_user_kapal', 'conf_user_id', 'm_kapal_id');
    }

    /**
     * Get the TTD attribute as URL
     */
    public function getTtdAttribute($value)
    {
        if ($value) {
            // Jika sudah berupa URL lengkap, return as is
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
            // Jika berupa path file, tambahkan base URL
            return url('images/ttd/' . $value);
        }
        return null;
    }
}
