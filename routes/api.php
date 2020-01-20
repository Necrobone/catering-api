<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('/provinces', 'ProvinceController@index');
    Route::get('/roles', 'RoleController@index');
    Route::apiResources([
        'headquarters' => 'HeadquarterController',
        'suppliers' => 'SupplierController',
        'dishes' => 'DishController',
        'menus' => 'MenuController',
        'events' => 'EventController',
        'employees' => 'UserController',
    ]);

    Route::apiResource('services', 'ServiceController')->except([
        'store', 'destroy',
    ]);

    Route::match(['put', 'patch'], '/services/{service}/toggle', 'ServiceController@toggle')->name('toggle');
});
