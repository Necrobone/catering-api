<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\DestroyUser;
use App\Http\Requests\GetServices;
use App\Http\Requests\ShowEmployee;
use App\Http\Requests\StoreEmployee;
use App\Http\Requests\UpdateEmployee;
use App\Http\Resources\ServiceCollection;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserCollection;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AdminRequest $request
     * @return UserCollection
     */
    public function index(AdminRequest $request)
    {
        return new UserCollection(User::whereNotIn('role_id', [Role::USER])->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreEmployee $request
     * @return UserResource
     */
    public function store(StoreEmployee $request)
    {
        $user = new User();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->api_token = Str::random(60);
        $user->role_id = $request->role;

        $user->save();

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param ShowEmployee $request
     * @param int $id
     * @return UserResource
     */
    public function show(ShowEmployee $request, $id)
    {
        return new UserResource(User::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEmployee $request
     * @param int $id
     * @return UserResource
     */
    public function update(UpdateEmployee $request, $id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->role_id = $request->role;

        if (isset($request->password) && $request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUser $request
     * @param int $id
     * @return int
     */
    public function destroy(DestroyUser $request, $id)
    {
        return User::destroy($id);
    }

    /**
     * Display a listing of the services.
     *
     * @param GetServices $request
     * @param $id
     * @return ServiceCollection
     */
    public function services(GetServices $request, $id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

        return new ServiceCollection($user->services);
    }
}
