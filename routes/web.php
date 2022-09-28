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
Auth::routes();
Route::get('/', function () {
    return view('welcome');
});
Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('user/view/{id}', [App\Http\Controllers\HomeController::class, 'viewUser'])->name('viewUser');
    Route::match(['get','post'],'user/add', [App\Http\Controllers\HomeController::class, 'addUser'])->name('addUser');
    Route::match(['get','post'],'user/edit/{id}', [App\Http\Controllers\HomeController::class, 'editUser'])->name('editUser');
    Route::match(['get','post'],'user/delete/{id}', [App\Http\Controllers\HomeController::class, 'deleteUser'])->name('deleteUser');
});