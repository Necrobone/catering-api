<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Event;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\GetDishes;
use App\Http\Requests\PersistDish;
use App\Http\Resources\Dish as DishResource;
use App\Http\Resources\DishCollection;
use App\Supplier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param GetDishes $request
     * @return DishCollection
     */
    public function index(GetDishes $request)
    {
        return new DishCollection(Dish::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PersistDish $request
     * @return DishResource
     */
    public function store(PersistDish $request)
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

        return new DishResource($dish);
    }

    /**
     * Display the specified resource.
     *
     * @param AdminRequest $request
     * @param int $id
     * @return DishResource
     */
    public function show(AdminRequest $request, $id)
    {
        return new DishResource(Dish::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PersistDish $request
     * @param int $id
     * @return DishResource
     */
    public function update(PersistDish $request, $id)
    {
        /** @var Dish $dish */
        $dish = Dish::findOrFail($id);

        $dish->name = $request->name;
        $dish->description = $request->description;
        if ($request->image !== $dish->image) {
            $fileName = Str::random(10) . $id . '.jpg';
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

        return new DishResource($dish);
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
        Dish::findOrFail($id);

        return Dish::destroy($id);
    }
}
