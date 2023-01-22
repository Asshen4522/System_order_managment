<?php

use App\Http\Controllers\getDataController;
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


Route::get('/Get_locomotives', [getDataController::class, 'Get_locomotives']);
Route::get('/Get_contacts', [getDataController::class, 'Get_contacts']);
Route::get('/Get_executors', [getDataController::class, 'Get_executors']);
