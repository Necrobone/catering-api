<?php

namespace App\Http\Requests;

use App\Exceptions\APIValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class LoginUser extends FormRequest
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
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
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
