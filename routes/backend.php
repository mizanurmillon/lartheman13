<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\TeamController;
use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\SecurityEventController;
use App\Http\Controllers\Web\Backend\TrainingProgramController;



Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

//FAQ Routes
Route::controller(CategoryController::class)->group(function () {
    Route::get('/category', 'index')->name('admin.categories.index');
    Route::get('/category/create', 'create')->name('admin.categories.create');
    Route::post('/category/store', 'store')->name('admin.categories.store');
    Route::get('/category/edit/{id}', 'edit')->name('admin.categories.edit');
    Route::post('/category/update/{id}', 'update')->name('admin.categories.update');
    Route::post('/category/status/{id}', 'status')->name('admin.categories.status');
    Route::post('/category/destroy/{id}', 'destroy')->name('admin.categories.destroy');
});

Route::controller(SecurityEventController::class)->group(function () {
    Route::get('/security-event', 'index')->name('admin.security_events.index');
    Route::post('/security-event/store', 'store')->name('admin.security_events.store');
    Route::get('/security-event/edit/{id}', 'edit')->name('admin.security_events.edit');
    Route::post('/security-event/verify/{id}', 'verify')->name('admin.security_events.verify');
});

Route::controller(TrainingProgramController::class)->group(function () {
    Route::get('/training-program', 'index')->name('admin.training_programs.index');
    Route::get('/training-program/create', 'create')->name('admin.training_programs.create');
    Route::post('/training-program/store', 'store')->name('admin.training_programs.store');
    Route::get('/training-program/edit/{id}', 'edit')->name('admin.training_programs.edit');
    Route::post('/training-program/update/{id}', 'update')->name('admin.training_programs.update');
    Route::post('/training-program/status/{id}', 'status')->name('admin.training_programs.status');
    Route::post('/training-program/destroy/{id}', 'destroy')->name('admin.training_programs.destroy');
});

Route::controller(TeamController::class)->group(function () {
    Route::get('/team', 'index')->name('admin.team.index');
});
