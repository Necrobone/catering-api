<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Event;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\PersistMenu;
use App\Http\Resources\Menu as MenuResource;
use App\Http\Resources\MenuCollection;
use App\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AdminRequest $request
     * @return MenuCollection
     */
    public function index(AdminRequest $request)
    {
        return new MenuCollection(Menu::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PersistMenu $request
     * @return MenuResource
     */
    public function store(PersistMenu $request)
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

        return new MenuResource($menu);
    }

    /**
     * Display the specified resource.
     *
     * @param AdminRequest $request
     * @param int $id
     * @return MenuResource
     */
    public function show(AdminRequest $request, $id)
    {
        return new MenuResource(Menu::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PersistMenu $request
     * @param int $id
     * @return MenuResource
     */
    public function update(PersistMenu $request, $id)
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

        return new MenuResource($menu);
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
        Menu::findOrFail($id);

        return Menu::destroy($id);
    }
}
