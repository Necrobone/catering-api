<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Headquarter extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'zip', 'city', 'province_id',
    ];

    /**
     * Get the province that owns the headquarter.
     */
    public function province()
    {
        return $this->belongsTo('App\Province');
    }

    /**
     * The suppliers that belong to the headquarter.
     */
    public function suppliers()
    {
        return $this->belongsToMany('App\Supplier', 'supplier_headquarters');
    }
}
