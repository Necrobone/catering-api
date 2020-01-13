<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The headquarters that belong to the supplier.
     */
    public function headquarters()
    {
        return $this->belongsToMany('App\Headquarter', 'supplier_headquarters');
    }

    /**
     * The dishes that belong to the supplier.
     */
    public function dishes()
    {
        return $this->belongsToMany('App\Dish', 'supplier_dishes');
    }
}
