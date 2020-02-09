<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\PersistEvent;
use App\Http\Resources\Event as EventResource;
use App\Http\Resources\EventCollection;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return EventCollection
     */
    public function index()
    {
        return new EventCollection(Event::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PersistEvent $request
     * @return EventResource
     */
    public function store(PersistEvent $request)
    {
        $event = new Event();

        $event->name = $request->name;

        $event->save();

        return new EventResource($event);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return EventResource
     */
    public function show($id)
    {
        return new EventResource(Event::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PersistEvent $request
     * @param int $id
     * @return EventResource
     */
    public function update(PersistEvent $request, $id)
    {
        /** @var Event $event */
        $event = Event::findOrFail($id);

        $event->name = $request->name;

        $event->save();

        return new EventResource($event);
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
        Event::findOrFail($id);

        return Event::destroy($id);
    }
}
