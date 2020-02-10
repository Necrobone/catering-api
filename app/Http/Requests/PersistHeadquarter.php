<?php

namespace App\Http\Requests;

use App\Exceptions\APIValidationException;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class PersistHeadquarter extends FormRequest
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
            'name'     => [
                'required',
                'string',
                'max:255',
            ],
            'address'  => [
                'required',
                'string',
                'max:255',
            ],
            'zip'      => [
                'required',
                'string',
                'max:255',
            ],
            'city'     => [
                'required',
                'string',
                'max:255',
            ],
            'province' => [
                'required',
                'integer',
                'exists:App\Province,id',
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
            'name.required'     => 'NAME_REQUIRED',
            'name.string'       => 'NAME_INVALID',
            'name.max'          => 'NAME_TOO_LONG',
            'address.required'  => 'ADDRESS_REQUIRED',
            'address.string'    => 'ADDRESS_INVALID',
            'address.max'       => 'ADDRESS_TOO_LONG',
            'zip.required'      => 'ZIP_REQUIRED',
            'zip.string'        => 'ZIP_INVALID',
            'zip.max'           => 'ZIP_TOO_LONG',
            'city.required'     => 'CITY_REQUIRED',
            'city.string'       => 'CITY_INVALID',
            'city.max'          => 'CITY_TOO_LONG',
            'province.required' => 'PROVINCE_REQUIRED',
            'province.integer'  => 'PROVINCE_INVALID',
            'province.exists'   => 'PROVINCE_NOT_FOUND',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @return void
     *
     * @throws APIValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new APIValidationException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
