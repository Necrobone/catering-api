<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Event;
use App\Http\Resources\Menu as MenuResource;
use App\Http\Resources\MenuCollection;
use App\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return MenuCollection
     */
    public function index()
    {
        return new MenuCollection(Menu::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Menu
     */
    public function store(Request $request)
    {
        $menu = new Menu();

        $menu->name = $request->name;

        $menu->save();

        $dishes = [];
        foreach ($request->dishes as $dish) {
            $dishes[] = Dish::findOrFail($dish);
        }

        $events = [];
        foreach ($request->events as $event) {
            $events[] = Event::findOrFail($event);
        }

        $menu->dishes()->saveMany($dishes);
        $menu->events()->saveMany($events);

        return $menu;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return MenuResource
     */
    public function show($id)
    {
        return new MenuResource(Menu::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Menu
     */
    public function update(Request $request, $id)
    {
        /** @var Menu $menu */
        $menu = Menu::findOrFail($id);

        $menu->name = $request->name;

        $menu->save();

        $dishes = [];
        foreach ($request->dishes as $dish) {
            $dishes[] = Dish::findOrFail($dish)->id;
        }

        $events = [];
        foreach ($request->events as $event) {
            $events[] = Event::findOrFail($event)->id;
        }

        $menu->dishes()->sync($dishes);
        $menu->events()->sync($events);

        return $menu;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy($id)
    {
        return Menu::destroy($id);
    }
}
