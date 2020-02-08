<?php

namespace App\Http\Requests;

use App\Exceptions\APIValidationException;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class PersistMenu extends FormRequest
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
            'dishes'   => [
                'required',
                'array',
            ],
            'dishes.*' => [
                'integer',
                'exists:App\Dish,id',
            ],
            'events'   => [
                'required',
                'array',
            ],
            'events.*' => [
                'integer',
                'exists:App\Event,id',
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
            'name.required'    => 'NAME_REQUIRED',
            'name.string'      => 'NAME_INVALID',
            'name.max'         => 'NAME_TOO_LONG',
            'dishes.required'  => 'DISHES_REQUIRED',
            'dishes.array'     => 'DISHES_INVALID',
            'dishes.*.integer' => 'DISHES_INVALID',
            'dishes.*.exists'  => 'DISHES_NOT_FOUND',
            'events.required'  => 'EVENTS_REQUIRED',
            'events.array'     => 'EVENTS_INVALID',
            'events.*.integer' => 'EVENTS_INVALID',
            'events.*.exists'  => 'EVENTS_NOT_FOUND',
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
