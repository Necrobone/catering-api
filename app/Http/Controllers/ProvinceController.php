<?php

namespace App\Http\Controllers;

use App\Province;
use Illuminate\Database\Eloquent\Collection;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Province[]|Collection
     */
    public function index()
    {
        return Province::all();
    }
}
