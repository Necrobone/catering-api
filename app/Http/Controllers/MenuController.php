<?php

namespace App\Http\Controllers;

use App\Http\Resources\Menu as MenuResource;
use App\Http\Resources\MenuCollection;
use App\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        return new MenuResource(Menu::find($id));
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
        $menu = Menu::find($id);

        $menu->name = $request->name;

        $menu->save();

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
