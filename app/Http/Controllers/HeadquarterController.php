<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidationException;
use App\Headquarter;
use App\Http\Resources\Headquarter as HeadquarterResource;
use App\Http\Resources\HeadquarterCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class HeadquarterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return HeadquarterCollection
     */
    public function index()
    {
        return new HeadquarterCollection(Headquarter::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Headquarter
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $headquarter = new Headquarter();

        $this->validateRequest($request);

        return $this->persist($request, $headquarter);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return HeadquarterResource
     */
    public function show($id)
    {
        return new HeadquarterResource(Headquarter::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Headquarter
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        /** @var Headquarter $headquarter */
        $headquarter = Headquarter::findOrFail($id);

        $this->validateRequest($request);

        return $this->persist($request, $headquarter);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return int
     */
    public function destroy($id)
    {
        Headquarter::findOrFail($id);

        return Headquarter::destroy($id);
    }

    /**
     * @param Request $request
     * @param Headquarter $headquarter
     * @return Headquarter
     */
    private function persist(Request $request, Headquarter $headquarter): Headquarter
    {
        $headquarter->name = $request->name;
        $headquarter->address = $request->address;
        $headquarter->zip = $request->zip;
        $headquarter->city = $request->city;
        $headquarter->province_id = $request->province;

        $headquarter->save();

        return $headquarter;
    }

    /**
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    private function validateRequest(Request $request): void
    {
        $payload = $request->only('name', 'address', 'zip', 'city', 'province');

        $rules = [
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

        $messages = [
            'name.required'     => 'NAME_REQUIRED',
            'name.string'       => 'NAME_INVALID',
            'name.max'          => 'NAME_TOO_LONG',
            'address.required'  => 'ADDRESS_REQUIRED',
            'address.string'    => 'ADDRESS_INVALID',
            'address.max'       => 'ADDRESS_TOO_LONG',
            'zip.required'      => 'ZIP_REQUIRED',
            'zip..string'       => 'ZIP_INVALID',
            'zip..max'          => 'ZIP_TOO_LONG',
            'city.required'     => 'CITY_REQUIRED',
            'city.string'       => 'CITY_INVALID',
            'city.max'          => 'CITY_TOO_LONG',
            'province.required' => 'PROVINCE_REQUIRED',
            'province.integer'  => 'PROVINCE_INVALID',
            'province.exists'   => 'PROVINCE_NOT_FOUND',
        ];

        $validator = Validator::make($payload, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
