<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * The dishes that belong to the service.
     */
    public function dishes()
    {
        return $this->belongsToMany('App\Dish', 'service_dishes');
    }
}
