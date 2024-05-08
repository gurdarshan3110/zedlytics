<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //dd(Auth::user());
    if (Auth::check()) {
        return redirect()->intended('/dashboard');
    }

    return view('auth/login');
});

Auth::routes();

Route::group(['middleware' => 'auth:web'], function ($router) {

    Route::resource('dashboard', DashboardController::class);


});
