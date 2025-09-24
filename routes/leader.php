<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChurchController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\AssignedVideoController;
use App\Http\Controllers\Api\ReportIncidentController;
use App\Http\Controllers\Api\RequestReportIncidentController;

Route::group(['middleware' => ['jwt.verify']], function () {

     Route::controller(TeamMemberController::class)->group(function () {
        Route::delete('/leave-team', 'leaveTeam');
     });
    
    Route::group(['middleware' => ['leader']], function () {

        Route::controller(ChurchController::class)->group(function () {
            Route::post('/create-church-profile', 'create');
        });

        Route::controller(RequestReportIncidentController::class)->group(function () {
            Route::get('/get-request-report-incident', 'allRequests');
            Route::get('/request-approved/{id}', 'requestApproved');
            Route::get('/request-rejected/{id}', 'requestRejected');
        });

        Route::controller(TeamMemberController::class)->group(function () {
            Route::get('/get-team-members', 'allTeamMembers');
            Route::get('/get-team-admin', 'allTeamAdmins');
            Route::delete('/remove-team-member/{id}', 'removeTeamMember');
        });

        Route::controller(ScheduleController::class)->group(function () {
           Route::post('/create-schedule', 'create'); 
           Route::get('/get-schedule', 'allSchedule');
           Route::get('/get-schedule/{id}', 'singleSchedule');
           Route::post('/update-schedule/{id}', 'updateSchedule');
           Route::get('/upcoming-schedule', 'upcomingSchedule');
           Route::delete('/delete-schedule/{id}', 'deleteSchedule');
           Route::delete('/assign-member/{id}', 'removeMember');
        });

        Route::controller(AssignedVideoController::class)->group(function () {
            Route::post('/assign-video', 'assignVideo');
            Route::get('/assigned-videos', 'allAssignedVideos');
            Route::get('/assigned-videos/{id}', 'singleAssignedVideo');
        });
    });

    Route::controller(ReportIncidentController::class)->group(function () {
        Route::post('/create-report-incident', 'create');
        Route::get('/get-report-incident', 'allReports');
        Route::get('/get-report-incident/{id}', 'singleReport');
        Route::get('/my-church-reports', 'myChurchReports');
    });
});
