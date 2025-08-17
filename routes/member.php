<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChurchController;
use App\Http\Controllers\Api\MyScheduleController;
use App\Http\Controllers\Api\RequestScheduleController;



Route::group(['middleware' => ['jwt.verify']], function () {

    
    Route::group(['middleware' => ['member']], function () {

        Route::controller(ChurchController::class)->group(function () {
            Route::get('/church-profile', 'getChurchProfile');
            Route::get('/church-profile/{id}', 'getChurchProfileById');
            Route::post('/join-church/{id}', 'joinChurch');
        });

        Route::controller(RequestScheduleController::class)->group(function () {
            Route::get('/request-schedule', 'index');
            Route::get('/request-schedule/{id}', 'singleSchedule');
            Route::get('/accept-schedule/{id}', 'acceptSchedule');
            Route::get('/decline-schedule/{id}', 'declineSchedule');
        });
        
        Route::controller(MyScheduleController::class)->group(function () {
            Route::get('/my-schedule', 'index');
        });

    });


});