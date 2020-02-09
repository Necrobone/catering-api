<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Service extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        return [
            "id"        => $this->id,
            "address"   => $this->address,
            "zip"       => $this->zip,
            "city"      => $this->city,
            "startDate" => $this->start_date_europe,
            "approved"  => $this->approved,
            "province"  => $this->province,
            "event"     => new Event($this->event),
            "dishes"    => new DishCollection($this->dishes),
            "users"     => new UserCollection($this->users),
        ];
    }
}
