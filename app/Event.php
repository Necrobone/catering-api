<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    /**
     * The dishes that belong to the event.
     */
    public function dishes()
    {
        return $this->belongsToMany('App\Dish', 'event_dishes');
    }
}
