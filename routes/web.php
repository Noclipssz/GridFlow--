<?php

use App\Http\Controllers\ProfBasicController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('prof/basic/login');
});

Route::get('/prof/login',      [ProfBasicController::class, 'showLogin'])->name('prof.basic.login');
Route::post('/prof/login',     [ProfBasicController::class, 'doLogin'])->name('prof.basic.login.post');

Route::get('/prof/cadastrar',  [ProfBasicController::class, 'showRegister'])->name('prof.basic.register');
Route::post('/prof/cadastrar', [ProfBasicController::class, 'doRegister'])->name('prof.basic.register.post');

Route::get('/prof/dashboard',  [ProfBasicController::class, 'dashboard'])->name('prof.basic.dashboard');
Route::post('/prof/logout',    [ProfBasicController::class, 'logout'])->name('prof.basic.logout');

