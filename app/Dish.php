<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dish extends Model
{
    use SoftDeletes;

    /**
     * The suppliers that belong to the dish.
     */
    public function suppliers()
    {
        return $this->belongsToMany('App\Supplier', 'supplier_dishes');
    }

    /**
     * The menus that belong to the dish.
     */
    public function menus()
    {
        return $this->belongsToMany('App\Menu', 'menu_dishes');
    }

    /**
     * The events that belong to the dish.
     */
    public function events()
    {
        return $this->belongsToMany('App\Event', 'event_dishes');
    }

    /**
     * The services that belong to the dish.
     */
    public function services()
    {
        return $this->belongsToMany('App\Service', 'service_dishes');
    }
}
