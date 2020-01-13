<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Http\Resources\Dish as DishResource;
use App\Http\Resources\DishCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $dish->image = $request->image;

        $dish->save();

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
        return new DishResource(Dish::find($id));
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
        $dish = Dish::find($id);

        $dish->name = $request->name;
        $dish->description = $request->description;
        $dish->image = $request->image;

        $dish->save();

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
