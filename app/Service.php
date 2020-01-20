<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * Get the province that owns the service.
     */
    public function province()
    {
        return $this->belongsTo('App\Province');
    }

    /**
     * Get the event that owns the service.
     */
    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    /**
     * The dishes that belong to the service.
     */
    public function dishes()
    {
        return $this->belongsToMany('App\Dish', 'service_dishes');
    }

    /**
     * The users that belong to the service.
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'user_services');
    }
}
