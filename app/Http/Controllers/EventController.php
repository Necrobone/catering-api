<?php

namespace App\Http\Controllers;

use App\Http\Resources\Event as EventResource;
use App\Http\Resources\EventCollection;
use App\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @param Request $request
     * @return Event
     */
    public function store(Request $request)
    {
        $event = new Event();

        $event->name = $request->name;

        $event->save();

        return $event;
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
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $event->name = $request->name;

        $event->save();

        return $event;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy($id)
    {
        return Event::destroy($id);
    }
}
