<?php

namespace App\Http\Controllers;

use App\Headquarter;
use App\Http\Resources\Headquarter as HeadquarterResource;
use App\Http\Resources\HeadquarterCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     */
    public function store(Request $request)
    {
        $headquarter = new Headquarter();

        $headquarter->name = $request->name;
        $headquarter->address = $request->address;
        $headquarter->zip = $request->zip;
        $headquarter->city = $request->city;
        $headquarter->province_id = $request->province;

        $headquarter->save();

        return $headquarter;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return HeadquarterResource
     */
    public function show($id)
    {
        return new HeadquarterResource(Headquarter::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $headquarter = Headquarter::find($id);

        $headquarter->name = $request->name;
        $headquarter->address = $request->address;
        $headquarter->zip = $request->zip;
        $headquarter->city = $request->city;
        $headquarter->province_id = $request->province;

        $headquarter->save();

        return $headquarter;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy($id)
    {
        return Headquarter::destroy($id);
    }
}
