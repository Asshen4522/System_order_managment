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
Route::get('/If_auth', [userController::class, 'If_auth']);
Route::get('/Logout', [userController::class, 'Logout']);
Route::post('/Ban', [userController::class, 'Ban']);
Route::post('/UnBan', [userController::class, 'UnBan']);

Route::post('/Create_order', [postDataController::class, 'Create_order']);
Route::post('/Edit_order', [postDataController::class, 'Edit_order']);
Route::post('/Create_contact', [postDataController::class, 'Create_contact']);
Route::post('/Create_report', [postDataController::class, 'Create_report']);
Route::post('/Edit_report', [postDataController::class, 'Edit_report']);
Route::post('/Create_cost', [postDataController::class, 'Create_cost']);
Route::post('/Create_activity', [postDataController::class, 'Create_activity']);
Route::post('/Update_status', [postDataController::class, 'Update_status']);
Route::post('/Delete_order', [postDataController::class, 'Delete_order']);
Route::post('/Delete_user', [postDataController::class, 'Delete_user']);
Route::post('/Edit_user', [postDataController::class, 'Edit_user']);
Route::post('/Create_model', [postDataController::class, 'Create_model']);


Route::get('/Get_locomotives', [getDataController::class, 'Get_locomotives']);
Route::get('/Get_contacts', [getDataController::class, 'Get_contacts']);
Route::get('/Get_executors', [getDataController::class, 'Get_executors']);
Route::post('/Get_orders', [getDataController::class, 'Get_orders']);
Route::get('/Get_my_orders', [getDataController::class, 'Get_my_orders']);
Route::post('/Get_display_order', [getDataController::class, 'Get_display_order']);
Route::post('/Get_report', [getDataController::class, 'Get_report']);
Route::post('/Get_display_report', [getDataController::class, 'Get_display_report']);
Route::get('/Get_costs', [getDataController::class, 'Get_costs']);
Route::get('/Get_activities', [getDataController::class, 'Get_activities']);
Route::post('/Get_costs_order', [getDataController::class, 'Get_costs_order']);
Route::post('/Get_order_report_dates', [getDataController::class, 'Get_order_report_dates']);
Route::post('/Get_wheel_pair', [getDataController::class, 'Get_wheel_pair']);
Route::post('/Get_inner_costs_report', [getDataController::class, 'Get_inner_costs_report']);
Route::post('/Get_reportList', [getDataController::class, 'Get_reportList']);
Route::get('/Get_userList', [getDataController::class, 'Get_userList']);
Route::post('/Get_user', [getDataController::class, 'Get_user']);
Route::get('/Get_stats', [getDataController::class, 'Get_stats']);
