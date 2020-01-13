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
}
