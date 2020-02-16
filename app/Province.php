<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the headquarters for the province.
     */
    public function headquarters()
    {
        return $this->hasMany('App\Headquarter');
    }

    /**
     * Get the services for the province.
     */
    public function services()
    {
        return $this->hasMany('App\Service');
    }
}
