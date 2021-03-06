<?php

namespace App\Http\Requests;

use App\Exceptions\APIValidationException;
use App\Role;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreEmployee extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var User $user */
        $user = $this->user();
        return $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'firstName' => [
                'required',
                'string',
                'max:255',
            ],
            'lastName'  => [
                'required',
                'string',
                'max:255',
            ],
            'email'     => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:App\User,email',
            ],
            'password'  => [
                'required',
                'string',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'min:8',
                'max:255',
            ],
            'role'      => [
                'required',
                'integer',
                'exists:App\Role,id',
                'in:' . Role::EMPLOYEE . ',' . Role::ADMINISTRATOR
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'firstName.required' => 'FIRST_NAME_REQUIRED',
            'firstName.string'   => 'FIRST_NAME_INVALID',
            'firstName.max'      => 'FIRST_NAME_TOO_LONG',
            'lastName.required'  => 'LAST_NAME_REQUIRED',
            'lastName.string'    => 'LAST_NAME_INVALID',
            'lastName.max'       => 'LAST_NAME_TOO_LONG',
            'email.required'     => 'EMAIL_REQUIRED',
            'email.string'       => 'EMAIL_INVALID',
            'email.email'        => 'EMAIL_INVALID',
            'email.max'          => 'EMAIL_TOO_LONG',
            'email.unique'       => 'EMAIL_EXISTS',
            'password.required'  => 'PASSWORD_REQUIRED',
            'password.string'    => 'PASSWORD_INVALID',
            'password.regex'     => 'PASSWORD_INVALID',
            'password.min'       => 'PASSWORD_TOO_SHORT',
            'password.max'       => 'PASSWORD_TOO_LONG',
            'role.required'      => 'ROLE_REQUIRED',
            'role.integer'       => 'ROLE_INVALID',
            'role.exists'        => 'ROLE_NOT_FOUND',
            'role.in'            => 'ROLE_INVALID',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws APIValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new APIValidationException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
