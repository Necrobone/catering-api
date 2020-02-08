<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotFoundException;
use App\Http\Requests\LoginUser;
use App\Http\Requests\SignupUser;
use App\Http\Resources\User as UserResource;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param LoginUser $request
     *
     * @return UserResource
     * @throws UserNotFoundException
     */
    public function login(LoginUser $request)
    {
        $payload = $request->only('email', 'password');

        $payload['deleted_at'] = null;

        if (Auth::validate($payload) === false) {
            throw new UserNotFoundException('LOGIN_ERROR', Response::HTTP_FORBIDDEN);
        }

        Auth::once($payload);

        return new UserResource(Auth::user());
    }

    /**
     * @param SignupUser $request
     * @return UserResource
     */
    public function signup(SignupUser $request)
    {
        $user = new User();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->api_token = Str::random(60);
        $user->role_id = Role::USER;

        $user->save();

        return new UserResource($user);
    }
}
