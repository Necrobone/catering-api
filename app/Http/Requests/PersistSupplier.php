<?php

namespace App\Http\Requests;

use App\Exceptions\APIValidationException;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class PersistSupplier extends FormRequest
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
            'name'           => [
                'required',
                'string',
                'max:255',
            ],
            'headquarters'   => [
                'required',
                'array',
            ],
            'headquarters.*' => [
                'integer',
                'exists:App\Headquarter,id',
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
            'name.required'          => 'NAME_REQUIRED',
            'name.string'            => 'NAME_INVALID',
            'name.max'               => 'NAME_TOO_LONG',
            'headquarters.required'  => 'HEADQUARTERS_REQUIRED',
            'headquarters.array'     => 'HEADQUARTERS_INVALID',
            'headquarters.*.integer' => 'HEADQUARTERS_INVALID',
            'headquarters.*.exists'  => 'HEADQUARTERS_NOT_FOUND',
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
