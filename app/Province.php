<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
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
