<?php

namespace App\Http\Requests;

use App\Exceptions\APIValidationException;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class PersistDish extends FormRequest
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
            'name'        => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
                'max:65535',
            ],
            'image'       => [
                'required',
                'string',
            ],
            'suppliers'   => [
                'required',
                'array',
            ],
            'suppliers.*' => [
                'integer',
                'exists:App\Supplier,id',
            ],
            'events'      => [
                'required',
                'array',
            ],
            'events.*'    => [
                'integer',
                'exists:App\Event,id',
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
            'name.required'        => 'NAME_REQUIRED',
            'name.string'          => 'NAME_INVALID',
            'name.max'             => 'NAME_TOO_LONG',
            'description.required' => 'DESCRIPTION_REQUIRED',
            'description.string'   => 'DESCRIPTION_INVALID',
            'description.max'      => 'DESCRIPTION_TOO_LONG',
            'image.required'       => 'IMAGE_REQUIRED',
            'image.string'         => 'IMAGE_INVALID',
            'suppliers.required'   => 'SUPPLIERS_REQUIRED',
            'suppliers.array'      => 'SUPPLIERS_INVALID',
            'suppliers.*.integer'  => 'SUPPLIERS_INVALID',
            'suppliers.*.exists'   => 'SUPPLIERS_NOT_FOUND',
            'events.required'      => 'EVENTS_REQUIRED',
            'events.array'         => 'EVENTS_INVALID',
            'events.*.integer'     => 'EVENTS_INVALID',
            'events.*.exists'      => 'EVENTS_NOT_FOUND',
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
