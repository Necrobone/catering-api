<?php

namespace App\Http\Requests;

use App\Exceptions\APIValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

abstract class PersistService extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address'   => [
                'required',
                'string',
                'max:255',
            ],
            'zip'       => [
                'required',
                'string',
                'max:255',
            ],
            'city'      => [
                'required',
                'string',
                'max:255',
            ],
            'startDate' => [
                'required',
                'date',
                'after:now',
            ],
            'approved'  => [
                'present',
                'nullable',
                'boolean',
            ],
            'province'  => [
                'required',
                'integer',
                'exists:App\Province,id',
            ],
            'event'     => [
                'required',
                'integer',
                'exists:App\Event,id',
            ],
            'dishes'    => [
                'required',
                'array',
            ],
            'dishes.*'  => [
                'integer',
                'exists:App\Dish,id',
            ],
            'users'     => [
                'required',
                'array',
            ],
            'users.*'   => [
                'integer',
                'exists:App\User,id',
            ]
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
            'address.required'   => 'ADDRESS_REQUIRED',
            'address.string'     => 'ADDRESS_INVALID',
            'address.max'        => 'ADDRESS_TOO_LONG',
            'zip.required'       => 'ZIP_REQUIRED',
            'zip.string'         => 'ZIP_INVALID',
            'zip.max'            => 'ZIP_TOO_LONG',
            'city.required'      => 'CITY_REQUIRED',
            'city.string'        => 'CITY_INVALID',
            'city.max'           => 'CITY_TOO_LONG',
            'startDate.required' => 'START_DATE_REQUIRED',
            'startDate.date'     => 'START_DATE_INVALID',
            'startDate.after'    => 'START_DATE_PAST',
            'approved.present'   => 'APPROVED_REQUIRED',
            'approved.boolean'   => 'APPROVED_INVALID',
            'province.required'  => 'PROVINCE_REQUIRED',
            'province.integer'   => 'PROVINCE_INVALID',
            'province.exists'    => 'PROVINCE_NOT_FOUND',
            'event.required'     => 'EVENT_REQUIRED',
            'event.integer'      => 'EVENT_INVALID',
            'event.exists'       => 'EVENT_NOT_FOUND',
            'dishes.required'    => 'DISHES_REQUIRED',
            'dishes.array'       => 'DISHES_INVALID',
            'dishes.*.integer'   => 'DISHES_INVALID',
            'dishes.*.exists'    => 'DISHES_NOT_FOUND',
            'users.required'     => 'USERS_REQUIRED',
            'users.array'        => 'USERS_INVALID',
            'users.*.integer'    => 'USERS_INVALID',
            'users.*.exists'     => 'USERS_NOT_FOUND',
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
