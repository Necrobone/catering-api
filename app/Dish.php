<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    /**
     * The suppliers that belong to the dish.
     */
    public function suppliers()
    {
        return $this->belongsToMany('App\Supplier', 'supplier_dishes');
    }
}
