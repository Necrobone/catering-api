<?php

namespace App\Http\Controllers;

use App\Headquarter;
use App\Http\Requests\PersistHeadquarter;
use App\Http\Resources\Headquarter as HeadquarterResource;
use App\Http\Resources\HeadquarterCollection;
use Illuminate\Http\Request;

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
     * @param PersistHeadquarter $request
     * @return HeadquarterResource
     */
    public function store(PersistHeadquarter $request)
    {
        $headquarter = new Headquarter();

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
     * @param PersistHeadquarter $request
     * @param int $id
     * @return HeadquarterResource
     */
    public function update(PersistHeadquarter $request, $id)
    {
        /** @var Headquarter $headquarter */
        $headquarter = Headquarter::findOrFail($id);

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
     * @return HeadquarterResource
     */
    private function persist(Request $request, Headquarter $headquarter): HeadquarterResource
    {
        $headquarter->name = $request->name;
        $headquarter->address = $request->address;
        $headquarter->zip = $request->zip;
        $headquarter->city = $request->city;
        $headquarter->province_id = $request->province;

        $headquarter->save();

        return new HeadquarterResource($headquarter);
    }
}
