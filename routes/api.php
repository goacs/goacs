<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function() {
    Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login');
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
    Route::post('/refresh', [\App\Http\Controllers\Auth\AuthController::class, 'refresh']);
});

Route::prefix('dashboard')->group(function() {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index']);
});

Route::apiResource('device', \App\Http\Controllers\Device\DeviceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
Route::post('device/{device}/addobject', [\App\Http\Controllers\Device\DeviceController::class, 'addObject']);
Route::get('device/{device}/provision', [\App\Http\Controllers\Device\DeviceController::class, 'provision']);
Route::get('device/{device}/lookup', [\App\Http\Controllers\Device\DeviceController::class, 'lookup']);
Route::get('/device/{device}/parameters/cached/download', [\App\Http\Controllers\Device\DeviceCachedParametersController::class, 'download']);
Route::delete('device/{device}/cache', [\App\Http\Controllers\Device\DeviceController::class, 'clearCache']);
Route::get('/device/{device}/parameters/cached', [\App\Http\Controllers\Device\DeviceCachedParametersController::class, 'index']);
Route::patch('/device/{device}/parameters/patch', [\App\Http\Controllers\Device\DeviceParameterController::class, 'patchParameters']);
Route::apiResource('device.parameters', \App\Http\Controllers\Device\DeviceParameterController::class);
Route::apiResource('device.templates', \App\Http\Controllers\Device\DeviceTemplateController::class)->only(['index', 'store', 'destroy']);
Route::apiResource('device.tasks', \App\Http\Controllers\Device\DeviceTaskController::class);
Route::get('/device/{device}/logs/download', [\App\Http\Controllers\Device\DeviceLogsController::class, 'downloadLogs']);
Route::apiResource('device.logs', \App\Http\Controllers\Device\DeviceLogsController::class)->only(['index']);
Route::get('provision/{provision}/clone', [\App\Http\Controllers\Provision\ProvisionController::class, 'clone']);
Route::apiResource('provision', \App\Http\Controllers\Provision\ProvisionController::class);

Route::apiResource('template', \App\Http\Controllers\Template\TemplateController::class);
Route::apiResource('faults', \App\Http\Controllers\Faults\FaultsController::class)->only(['index']);
Route::apiResource('template.parameters', \App\Http\Controllers\Template\TemplateParameterController::class);
Route::prefix('settings')->group(function() {
    Route::apiResource('tasks', \App\Http\Controllers\Settings\Tasks\TaskController::class);
    Route::apiResource('user', \App\Http\Controllers\Settings\Users\UserController::class);
    Route::apiResource('debug', \App\Http\Controllers\Settings\DebugController::class)->only(['index', 'store']);
});

Route::apiResource('file', \App\Http\Controllers\File\FileController::class);
Route::get('/file/{file}/download', [\App\Http\Controllers\File\FileController::class, 'download'])->name('file.download');

Route::apiResource('settings', \App\Http\Controllers\Settings\SettingsController::class)->only(['index', 'store']);

