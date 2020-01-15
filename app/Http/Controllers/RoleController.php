<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Role[]|Collection
     */
    public function index()
    {
        return Role::all();
    }
}
