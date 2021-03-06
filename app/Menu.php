<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    /**
     * The dishes that belong to the menu.
     */
    public function dishes()
    {
        return $this->belongsToMany('App\Dish', 'menu_dishes');
    }

    /**
     * The events that belong to the menu.
     */
    public function events()
    {
        return $this->belongsToMany('App\Event', 'event_menus');
    }
}
