<?php

use App\Http\Controllers\ProfBasicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminScheduleController;


Route::get('/', function () {
    return view('prof/basic/login');
});

Route::get('/admin/grade', [AdminScheduleController::class, 'form'])->name('admin.grade.form');
Route::post('/admin/grade/generate', [AdminScheduleController::class, 'generate'])->name('admin.grade.generate');


Route::get('/prof/login',      [ProfBasicController::class, 'showLogin'])->name('prof.basic.login');
Route::post('/prof/login',     [ProfBasicController::class, 'doLogin'])->name('prof.basic.login.post');

Route::get('/prof/cadastrar',  [ProfBasicController::class, 'showRegister'])->name('prof.basic.register');
Route::post('/prof/cadastrar', [ProfBasicController::class, 'doRegister'])->name('prof.basic.register.post');

Route::get('/prof/dashboard',  [ProfBasicController::class, 'dashboard'])->name('prof.basic.dashboard');
Route::post('/prof/logout',    [ProfBasicController::class, 'logout'])->name('prof.basic.logout');

Route::get('/prof/horario',  [ProfBasicController::class, 'showSchedule'])->name('prof.basic.schedule');
Route::post('/prof/horario', [ProfBasicController::class, 'saveSchedule'])->name('prof.basic.schedule.save');


