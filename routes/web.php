<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [UserController::class, 'index'])->name('users.index');
Route::get('users', [UserController::class, 'index'])->name('users.index');
Route::post('create-user', [UserController::class, 'store'])->name('users.store');
Route::get('user-info/{id}', [UserController::class, 'userInfo'])->name('users.userInfo');
Route::post('edit-user', [UserController::class, 'update'])->name('users.update');
Route::delete('delete-user/{id}', [UserController::class, 'destroy'])->name('users.destroy');
