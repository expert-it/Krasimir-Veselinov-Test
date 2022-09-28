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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', [App\Http\Controllers\ApiController::class, 'getUsers']);
Route::get('user/detail/{id}', [App\Http\Controllers\ApiController::class, 'viewUser']);
Route::post('user/add', [App\Http\Controllers\ApiController::class, 'addUser']);
Route::post('user/edit/{id}', [App\Http\Controllers\ApiController::class, 'editUser']);
Route::get('user/delete/{id}', [App\Http\Controllers\ApiController::class, 'deleteUser']);