<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
//Route::get('/', function () {
    ///return view('welcome');
//});
// ----- Admin -----
Route::get('/', [HomeController::class, 'home']);


Route::get('admin', [AuthController::class, 'login_admin']);
Route::post('admin', [AuthController::class, 'auth_login_admin']);
Route::get('admin/logout', [AuthController::class, 'logout_admin']);

// ----- User -----
Route::post('auth_register', [AuthController::class, 'auth_register']);
Route::post('auth_login', [AuthController::class, 'auth_login']);