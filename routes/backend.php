<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\TeamController;
use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\IncidentTypeController;
use App\Http\Controllers\Web\Backend\LocationController;
use App\Http\Controllers\Web\Backend\ReportController;
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

Route::controller(IncidentTypeController::class)->group(function () {
    Route::get('/incident-type', 'index')->name('admin.incident_types.index');
    Route::get('/incident-type/create', 'create')->name('admin.incident_types.create');
    Route::post('/incident-type/store', 'store')->name('admin.incident_types.store');
    Route::get('/incident-type/edit/{id}', 'edit')->name('admin.incident_types.edit');
    Route::post('/incident-type/update/{id}', 'update')->name('admin.incident_types.update');
    Route::post('/incident-type/toggle-share/{id}', 'toggleShare')->name('admin.incident_types.toggleShare');
    Route::post('/incident-type/destroy/{id}', 'destroy')->name('admin.incident_types.destroy');
});

Route::controller(LocationController::class)->group(function () {
    Route::get('/location', 'index')->name('admin.locations.index');
    Route::get('/location/create', 'create')->name('admin.locations.create');
    Route::post('/location/store', 'store')->name('admin.locations.store');
    Route::get('/location/edit/{id}', 'edit')->name('admin.locations.edit');
    Route::post('/location/update/{id}', 'update')->name('admin.locations.update');
    Route::post('/location/destroy/{id}', 'destroy')->name('admin.locations.destroy');
});

Route::controller(SecurityEventController::class)->group(function () {
    Route::get('/security-event', 'index')->name('admin.security_events.index');
    Route::post('/security-event/store', 'store')->name('admin.security_events.store');
    Route::get('/security-event/edit/{id}', 'edit')->name('admin.security_events.edit');
    Route::get('/security-event/show/{id}', 'show')->name('admin.security_events.show');
    Route::post('/security-event/update/{id}', 'update')->name('admin.security_events.update');
    Route::post('/security-event/verify/{id}', 'verify')->name('admin.security_events.verify');
    Route::get('/incident-types/by-category', 'getIncidentTypes')->name('admin.incident_types.by_category');

    Route::get('/security-event/view/{id}', 'view')->name('admin.security_events.view');
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
    Route::get('/team/show/{id}', 'show')->name('admin.team.show');
});

Route::controller(ReportController::class)->group(function () {
    Route::get('/reports', 'index')->name('admin.reports.index');
});

Route::patch('security-events/{id}/toggle-pin', [SecurityEventController::class, 'togglePin'])
    ->name('admin.security_events.toggle_pin');

