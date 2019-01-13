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


Route::post('/add_data', 'API\DesktopController@add_data');
Route::post('/load_data', 'API\DesktopController@load_data');
Route::post('/update_data', 'API\DesktopController@update_data');


Route::post('/administrator_load_data', 'API\DesktopController@administrator_load_data');
Route::post('/administrator_add_data', 'API\DesktopController@administrator_add_data');
Route::post('/administrator_update_data', 'API\DesktopController@administrator_update_data');
Route::post('/administrator_delete_data', 'API\DesktopController@administrator_delete_data');
Route::post('/administrator_reset_password', 'API\DesktopController@administrator_reset_password');

Route::post('/employee_resign', 'API\DesktopController@employee_resign');
Route::post('/employee_load_data', 'API\DesktopController@employee_load_data');
Route::post('/employee_add_data', 'API\DesktopController@employee_add_data');
Route::post('/employee_update_data', 'API\DesktopController@employee_update_data');
Route::post('/employee_upload_image', 'API\DesktopController@employee_upload_image');
Route::post('/employee_add_image_data', 'API\DesktopController@employee_add_image_data');
Route::post('/employee_update_image_data', 'API\DesktopController@employee_update_image_data');

/**
 * END DESKTOP
 */