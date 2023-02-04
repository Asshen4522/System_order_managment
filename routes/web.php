<?php

use App\Http\Controllers\getDataController;
use App\Http\Controllers\postDataController;
use App\Http\Controllers\userController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/Authorizate', [userController::class, 'Authorizate']);
Route::post('/Register', [userController::class, 'Register']);

Route::post('/Create_order', [postDataController::class, 'Create_order']);
Route::post('/Create_contact', [postDataController::class, 'Create_contact']);
Route::post('/Create_report', [postDataController::class, 'Create_report']);
Route::post('/Create_cost', [postDataController::class, 'Create_cost']);
Route::post('/Create_activity', [postDataController::class, 'Create_activity']);


Route::get('/Get_locomotives', [getDataController::class, 'Get_locomotives']);
Route::get('/Get_contacts', [getDataController::class, 'Get_contacts']);
Route::get('/Get_executors', [getDataController::class, 'Get_executors']);
Route::get('/Get_orders', [getDataController::class, 'Get_orders']);
Route::get('/Get_my_orders', [getDataController::class, 'Get_my_orders']);
Route::post('/Get_display_order', [getDataController::class, 'Get_display_order']);
Route::get('/Get_costs', [getDataController::class, 'Get_costs']);
Route::get('/Get_activities', [getDataController::class, 'Get_activities']);
Route::post('/Get_costs_order', [getDataController::class, 'Get_costs_order']);
Route::post('/Get_order_report_dates', [getDataController::class, 'Get_order_report_dates']);
