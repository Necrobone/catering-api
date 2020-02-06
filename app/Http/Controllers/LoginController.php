<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotFoundException;
use App\Exceptions\ValidationException;
use App\Http\Resources\User as UserResource;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param Request $request
     *
     * @return UserResource
     * @throws UserNotFoundException
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $payload = $request->only('email', 'password');

        $rules = [
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                'exists:App\User,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
            ],
        ];

        $messages = [
            'email.required'    => 'EMAIL_REQUIRED',
            'email.string'      => 'EMAIL_INVALID',
            'email.email'       => 'EMAIL_INVALID',
            'email.max'         => 'EMAIL_TOO_LONG',
            'email.exists'      => 'EMAIL_NOT_FOUND',
            'password.required' => 'PASSWORD_REQUIRED',
            'password.string'   => 'PASSWORD_INVALID',
            'password.min'      => 'PASSWORD_TOO_SHORT',
            'password.max'      => 'PASSWORD_TOO_LONG',
        ];

        $validator = Validator::make($payload, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $payload['deleted_at'] = null;

        if (Auth::validate($payload) === false) {
            throw new UserNotFoundException('LOGIN_ERROR', Response::HTTP_FORBIDDEN);
        }

        Auth::once($payload);

        return new UserResource(Auth::user());
    }

    /**
     * @param Request $request
     * @return UserResource
     * @throws ValidationException
     */
    public function signup(Request $request)
    {
        $payload = $request->only('first_name', 'last_name', 'email', 'password', 'password_confirmation');

        $rules = [
            'first_name' => [
                'required',
                'string',
                'max:255',
            ],
            'last_name'  => [
                'required',
                'string',
                'max:255',
            ],
            'email'      => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:App\User,email',
            ],
            'password'   => [
                'required',
                'confirmed',
                'string',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'min:8',
                'max:255',
            ],
        ];

        $messages = [
            'first_name.required' => 'FIRST_NAME_REQUIRED',
            'first_name.string'   => 'FIRST_NAME_INVALID',
            'first_name.max'      => 'FIRST_NAME_TOO_LONG',
            'last_name.required'  => 'LAST_NAME_REQUIRED',
            'last_name.string'    => 'LAST_NAME_INVALID',
            'last_name.max'       => 'LAST_NAME_TOO_LONG',
            'email.required'      => 'EMAIL_REQUIRED',
            'email.string'        => 'EMAIL_INVALID',
            'email.email'         => 'EMAIL_INVALID',
            'email.max'           => 'EMAIL_TOO_LONG',
            'email.unique'        => 'EMAIL_EXISTS',
            'password.required'   => 'PASSWORD_REQUIRED',
            'password.confirmed'  => 'PASSWORD_UNMATCHED',
            'password.string'     => 'PASSWORD_INVALID',
            'password.regex'      => 'PASSWORD_INVALID',
            'password.min'        => 'PASSWORD_TOO_SHORT',
            'password.max'        => 'PASSWORD_TOO_LONG',
        ];

        $validator = Validator::make($payload, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

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
