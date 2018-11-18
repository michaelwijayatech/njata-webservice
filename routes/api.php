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

/**
 * TEST FOR GET AND POST
 */
Route::get('/test_get', 'API\TestController@get');
Route::post('/test_post', 'API\TestController@post');

/**
 * START DESKTOP
 */

Route::post('/uploadFile', 'API\DesktopController@uploadFile');
Route::post('/signin', 'API\DesktopController@signin');
Route::post('/administrator_reset_password', 'API\DesktopController@administrator_reset_password');

/**
 * END DESKTOP
 */