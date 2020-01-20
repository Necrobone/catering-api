<?php

namespace App\Http\Controllers;

use App\Http\Resources\Service as ServiceResource;
use App\Http\Resources\ServiceCollection;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ServiceCollection
     */
    public function index()
    {
        return new ServiceCollection(Service::all());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ServiceResource
     */
    public function show($id)
    {
        return new ServiceResource(Service::find($id));
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
        $service = Service::find($id);

        $service->address = $request->address;
        $service->zip = $request->zip;
        $service->city = $request->city;
        $service->start_date = $request->startDate;
        $service->province_id = $request->province;
        $service->event_id = $request->event;

        $service->save();

        return $service;
    }

    /**
     * Toggle the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function toggle(Request $request, $id)
    {
        $service = Service::find($id);

        $service->approved = $request->approved;

        $service->save();

        return $service;
    }
}
