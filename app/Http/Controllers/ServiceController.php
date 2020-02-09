<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Http\Requests\StoreService;
use App\Http\Requests\ToggleService;
use App\Http\Requests\UpdateService;
use App\Http\Resources\Service as ServiceResource;
use App\Http\Resources\ServiceCollection;
use App\Service;
use App\User;
use DateTimeImmutable;
use Exception;

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
     * Store a newly created resource in storage.
     *
     * @param StoreService $request
     * @return ServiceResource
     * @throws Exception
     */
    public function store(StoreService $request)
    {
        $service = new Service();

        $service->address = $request->address;
        $service->zip = $request->zip;
        $service->city = $request->city;
        $service->start_date = new DateTimeImmutable($request->startDate);
        $service->province_id = $request->province;
        $service->event_id = $request->event;

        $service->save();

        $dishes = [];
        foreach ($request->dishes as $dish) {
            $dishes[] = Dish::findOrFail($dish);
        }

        $users = [];
        foreach ($request->users as $user) {
            $users[] = User::findOrFail($user);
        }

        $service->dishes()->saveMany($dishes);
        $service->users()->saveMany($users);

        return new ServiceResource($service);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ServiceResource
     */
    public function show($id)
    {
        return new ServiceResource(Service::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateService $request
     * @param int $id
     * @return ServiceResource
     * @throws Exception
     */
    public function update(UpdateService $request, $id)
    {
        /** @var Service $service */
        $service = Service::findOrFail($id);

        $service->address = $request->address;
        $service->zip = $request->zip;
        $service->city = $request->city;
        $service->start_date = new DateTimeImmutable($request->startDate);
        $service->province_id = $request->province;
        $service->event_id = $request->event;

        $service->save();

        $dishes = [];
        foreach ($request->dishes as $dish) {
            $dishes[] = Dish::findOrFail($dish)->id;
        }

        $users = [];
        foreach ($request->users as $user) {
            $users[] = User::findOrFail($user)->id;
        }

        $service->dishes()->sync($dishes);
        $service->users()->sync($dishes);

        return new ServiceResource($service);
    }

    /**
     * Toggle the specified resource in storage.
     *
     * @param ToggleService $request
     * @param int $id
     * @return ServiceResource
     */
    public function toggle(ToggleService $request, $id)
    {
        /** @var Service $service */
        $service = Service::findOrFail($id);

        $service->approved = $request->approved;

        $service->save();

        return new ServiceResource($service);
    }
}
