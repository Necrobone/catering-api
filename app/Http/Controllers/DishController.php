<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Event;
use App\Http\Resources\Dish as DishResource;
use App\Http\Resources\DishCollection;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return DishCollection
     */
    public function index()
    {
        return new DishCollection(Dish::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Dish
     */
    public function store(Request $request)
    {
        $dish = new Dish();

        $dish->name = $request->name;
        $dish->description = $request->description;

        $fileName = Str::random(10) . '.jpg';
        $image = substr($request->image, strpos($request->image, ',') + 1);

        Storage::disk('public')->put($fileName, base64_decode($image));

        $dish->image = asset("storage/{$fileName}");

        $dish->save();

        $suppliers = [];
        foreach ($request->suppliers as $supplier) {
            $suppliers[] = Supplier::findOrFail($supplier);
        }

        $events = [];
        foreach ($request->events as $event) {
            $events[] = Event::findOrFail($event);
        }

        $dish->suppliers()->saveMany($suppliers);
        $dish->events()->saveMany($events);

        return $dish;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return DishResource
     */
    public function show($id)
    {
        return new DishResource(Dish::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Dish
     */
    public function update(Request $request, $id)
    {
        /** @var Dish $dish */
        $dish = Dish::findOrFail($id);

        $dish->name = $request->name;
        $dish->description = $request->description;
        if ($request->image !== $dish->image) {
            $fileName = Str::random(10) . '.jpg';
            $image = substr($request->image, strpos($request->image, ',') + 1);

            Storage::disk('public')->put($fileName, base64_decode($image));

            $dish->image = asset("storage/{$fileName}");
        }

        $dish->save();

        $suppliers = [];
        foreach ($request->suppliers as $supplier) {
            $suppliers[] = Supplier::findOrFail($supplier)->id;
        }

        $events = [];
        foreach ($request->events as $event) {
            $events[] = Event::findOrFail($event)->id;
        }

        $dish->suppliers()->sync($suppliers);
        $dish->events()->sync($events);

        return $dish;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy($id)
    {
        return Dish::destroy($id);
    }
}
