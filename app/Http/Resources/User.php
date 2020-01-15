<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"        => $this->id,
            "firstName" => $this->first_name,
            "lastName"  => $this->last_name,
            "email"     => $this->email,
            "password"  => $this->password,
            "apiToken"  => $this->api_token,
            "role"      => $this->role,
            "services"  => $this->services,
        ];
    }
}
