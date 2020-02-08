<?php

namespace App\Http\Requests;

use App\Exceptions\APIValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class SignupUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
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
