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

Route::post('/login', 'LoginController@login')->name('login');
Route::post('/signup', 'LoginController@signup')->name('signup');

Route::middleware('auth:api')->group(function () {
    Route::get('/provinces', 'ProvinceController@index')->name('provinces');
    Route::get('/roles', 'RoleController@index')->name('roles');
    Route::apiResources([
        'headquarters' => 'HeadquarterController',
        'suppliers' => 'SupplierController',
        'dishes' => 'DishController',
        'menus' => 'MenuController',
        'events' => 'EventController',
        'employees' => 'UserController',
    ]);

    Route::get('/employees/{employee}/services', 'UserController@services')->name('employees.services');

    Route::apiResource('services', 'ServiceController')->except('destroy');

    Route::match(['put', 'patch'], '/services/{service}/toggle', 'ServiceController@toggle')->name('services.toggle');
});
