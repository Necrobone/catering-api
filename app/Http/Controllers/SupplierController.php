<?php

namespace App\Http\Controllers;

use App\Headquarter;
use App\Http\Resources\Supplier as SupplierResource;
use App\Http\Resources\SupplierCollection;
use App\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return SupplierCollection
     */
    public function index()
    {
        return new SupplierCollection(Supplier::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Supplier
     */
    public function store(Request $request)
    {
        $supplier = new Supplier();

        $supplier->name = $request->name;

        $supplier->save();

        $headquarters = [];

        foreach ($request->headquarters as $headquarter) {
            $headquarters[] = Headquarter::findOrFail($headquarter);
        }

        $supplier->headquarters()->saveMany($headquarters);

        return $supplier;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return SupplierResource
     */
    public function show($id)
    {
        return new SupplierResource(Supplier::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Supplier
     */
    public function update(Request $request, $id)
    {
        /** @var Supplier $supplier */
        $supplier = Supplier::findOrFail($id);

        $supplier->name = $request->name;

        $supplier->save();

        $headquarters = [];

        foreach ($request->headquarters as $headquarter) {
            $headquarters[] = Headquarter::findOrFail($headquarter)->id;
        }

        $supplier->headquarters()->sync($headquarters);

        return $supplier;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy($id)
    {
        return Supplier::destroy($id);
    }
}
