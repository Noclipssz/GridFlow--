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
Route::post('/admin/grade/store', [AdminScheduleController::class, 'store'])->name('admin.grade.store');


Route::get('/prof/login',      [ProfBasicController::class, 'showLogin'])->name('prof.basic.login');
Route::post('/prof/login',     [ProfBasicController::class, 'doLogin'])->name('prof.basic.login.post');

Route::get('/prof/cadastrar',  [ProfBasicController::class, 'showRegister'])->name('prof.basic.register');
Route::post('/prof/cadastrar', [ProfBasicController::class, 'doRegister'])->name('prof.basic.register.post');

Route::get('/prof/dashboard',  [ProfBasicController::class, 'dashboard'])->name('prof.basic.dashboard');
Route::post('/prof/logout',    [ProfBasicController::class, 'logout'])->name('prof.basic.logout');

Route::get('/prof/horario',  [ProfBasicController::class, 'showSchedule'])->name('prof.basic.schedule');
Route::post('/prof/horario', [ProfBasicController::class, 'saveSchedule'])->name('prof.basic.schedule.save');




Route::prefix("admin/turmas")->group(function () {
    Route::get("/", [AdminTurmaController::class, "index"])->name("admin.turmas.index");
    Route::get("/create", [AdminTurmaController::class, "create"])->name("admin.turmas.create");
    Route::post("/", [AdminTurmaController::class, "store"])->name("admin.turmas.store");
    Route::get("/{turma}/edit", [AdminTurmaController::class, "edit"])->name("admin.turmas.edit");
    Route::put("/{turma}", [AdminTurmaController::class, "update"])->name("admin.turmas.update");
    Route::delete("/{turma}", [AdminTurmaController::class, "destroy"])->name("admin.turmas.destroy");
    Route::get("/{turma}/associar", [AdminTurmaController::class, "associar"])->name("admin.turmas.associar");
    Route::post("/{turma}/associar", [AdminTurmaController::class, "salvarAssociacao"])->name("admin.turmas.salvarAssociacao");
    Route::get("/{turma}/show", [AdminTurmaController::class, "show"])->name("admin.turmas.show");
});


