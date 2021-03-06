<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * The services that belong to the user.
     */
    public function services()
    {
        return $this->belongsToMany('App\Service', 'user_services');
    }

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role->id === Role::ADMINISTRATOR;
    }

    /**
     * @return bool
     */
    public function isEmployee()
    {
        return $this->role->id === Role::EMPLOYEE;
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        return $this->role->id === Role::USER;
    }

    /**
     * @return bool
     */
    public function isNotUser()
    {
        return $this->role->id !== Role::USER;
    }
}
