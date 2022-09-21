<?php

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

Route::post('/acs', [\App\Http\Controllers\ACSController::class, 'process']);
Route::get('/acs', [\App\Http\Controllers\ACSController::class, 'process']);


Route::get('{any}', fn() => view('app'))->where('any', '.*');
