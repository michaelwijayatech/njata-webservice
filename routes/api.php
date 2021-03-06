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
Route::post('/test_pdf', 'API\TestController@pdf');
Route::post('/test_post', 'API\TestController@post');

Route::get('/acsb', 'API\DesktopController@accountancy_check_saldo_before');
Route::get('/acid', 'API\DesktopController@accountancy_get_id_by_activity');
Route::get('/acc', 'API\DesktopController@accountancy');

/**
 * START DESKTOP
 */

Route::post('/uploadFile', 'API\DesktopController@uploadFile');

Route::post('/signin', 'API\DesktopController@signin');
Route::get('/test_push', 'API\DesktopController@sendPushNotification');

Route::post('/get_public_files', 'API\DesktopController@get_public_files');

Route::post('/add_data', 'API\DesktopController@add_data');
Route::post('/load_data', 'API\DesktopController@load_data');
Route::post('/update_data', 'API\DesktopController@update_data');
Route::post('/delete_data', 'API\DesktopController@delete_data');
Route::post('/destroy_data', 'API\DesktopController@destroy_data');
Route::post('/print_data', 'API\DesktopController@print_data');


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