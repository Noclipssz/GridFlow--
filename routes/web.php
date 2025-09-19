<?php

use App\Http\Controllers\ProfBasicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminScheduleController;
use App\Http\Controllers\AdminTurmaController;


Route::get('/', function () {
    return view('prof/basic/login');
});

Route::get('/admin/grade', [AdminScheduleController::class, 'form'])->name('admin.grade.form');
Route::post('/admin/grade/generate', [AdminScheduleController::class, 'generate'])->name('admin.grade.generate');
Route::post('/admin/grade/save', [AdminScheduleController::class, 'save'])->name('admin.grade.save');
Route::post('/admin/grade/turmas/{turma}/liberar', [AdminScheduleController::class, 'clear'])->name('admin.grade.clear');

// Admin • Turmas
Route::get('/admin/turmas', [AdminTurmaController::class, 'index'])->name('admin.turmas.index');
Route::post('/admin/turmas', [AdminTurmaController::class, 'store'])->name('admin.turmas.store');


Route::get('/prof/login',      [ProfBasicController::class, 'showLogin'])->name('prof.basic.login');
Route::post('/prof/login',     [ProfBasicController::class, 'doLogin'])->name('prof.basic.login.post');

Route::get('/prof/cadastrar',  [ProfBasicController::class, 'showRegister'])->name('prof.basic.register');
Route::post('/prof/cadastrar', [ProfBasicController::class, 'doRegister'])->name('prof.basic.register.post');

Route::get('/prof/dashboard',  [ProfBasicController::class, 'dashboard'])->name('prof.basic.dashboard');
Route::post('/prof/logout',    [ProfBasicController::class, 'logout'])->name('prof.basic.logout');

Route::get('/prof/horario',  [ProfBasicController::class, 'showSchedule'])->name('prof.basic.schedule');
Route::post('/prof/horario', [ProfBasicController::class, 'saveSchedule'])->name('prof.basic.schedule.save');

// Turmas do professor
Route::get('/prof/turmas', [ProfBasicController::class, 'listTurmas'])->name('prof.turmas.index');
Route::get('/prof/turmas/{turma}', [ProfBasicController::class, 'showTurma'])->name('prof.turmas.show');
