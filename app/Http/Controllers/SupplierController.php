<?php

namespace App\Http\Controllers;

use App\Headquarter;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\PersistSupplier;
use App\Http\Resources\Supplier as SupplierResource;
use App\Http\Resources\SupplierCollection;
use App\Supplier;

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
     * @param PersistSupplier $request
     * @return SupplierResource
     */
    public function store(PersistSupplier $request)
    {
        $supplier = new Supplier();

        $supplier->name = $request->name;

        $supplier->save();

        $headquarters = [];

        foreach ($request->headquarters as $headquarter) {
            $headquarters[] = Headquarter::findOrFail($headquarter);
        }

        $supplier->headquarters()->saveMany($headquarters);

        return new SupplierResource($supplier);
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
     * @param PersistSupplier $request
     * @param int $id
     * @return SupplierResource
     */
    public function update(PersistSupplier $request, $id)
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

        return new SupplierResource($supplier);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AdminRequest $request
     * @param int $id
     * @return int
     */
    public function destroy(AdminRequest $request, $id)
    {
        Supplier::findOrFail($id);

        return Supplier::destroy($id);
    }
}
