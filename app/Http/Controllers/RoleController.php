<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AdminRequest $request
     * @return Role[]|Collection
     */
    public function index(AdminRequest $request)
    {
        return Role::whereNotIn('id', [Role::USER])->get();
    }
}
