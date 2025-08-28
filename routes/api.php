<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\SocialLinkController;
use App\Http\Controllers\Api\DynamicPageController;
use App\Http\Controllers\Api\SitesettingController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ChurchProfileController;
use App\Http\Controllers\Api\Chat\GroupInfoController;
use App\Http\Controllers\Api\Chat\GetMessageController;
use App\Http\Controllers\Api\TrainingProgramController;
use App\Http\Controllers\Api\Chat\CreateGroupController;
use App\Http\Controllers\Api\Chat\SendMessageController;
use App\Http\Controllers\Api\Chat\GetConversationController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//Social Login
Route::post('/social-login', [SocialAuthController::class, 'socialLogin']);

//Register API
Route::controller(RegisterController::class)->prefix('users/register')->group(function () {
    // User Register
    Route::post('/', 'userRegister');

    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');
});

//Login API
Route::controller(LoginController::class)->prefix('users/login')->group(function () {

    // User Login
    Route::post('/', 'userLogin');

    // Verify Email
    Route::post('/email-verify', 'emailVerify');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');

    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');

    //Reset Password
    Route::post('/reset-password', 'resetPassword');
});

Route::controller(SitesettingController::class)->group(function () {
    Route::get('/site-settings', 'siteSettings');
});

//Dynamic Page
Route::controller(DynamicPageController::class)->group(function () {
    Route::get('/dynamic-pages', 'dynamicPages');
    Route::get('/dynamic-pages/single/{slug}', 'single');
});

//Social Links
Route::controller(SocialLinkController::class)->group(function () {
    Route::get('/social-links', 'socialLinks');
});

//FAQ APIs
Route::controller(FaqController::class)->group(function () {
    Route::get('/faq/all', 'FaqAll');
});

Route::group(['middleware' => ['jwt.verify']], function () {

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/data', 'userData');
        Route::post('/data/update', 'userUpdate');
        Route::post('/password/change', 'passwordChange');
        Route::post('/logout', 'logoutUser');
        Route::delete('/delete', 'deleteUser');
    });

    Route::controller(ChurchProfileController::class)->group(function () {
        Route::get('/my-church-profile', 'churchProfile');
        Route::post('/update-church-profile', 'updateChurchProfile');
    });

    //Chat Route will be here
    Route::post('/send-message', SendMessageController::class);
    Route::get('/chat/messages', GetMessageController::class);
    Route::get('/conversations', GetConversationController::class);

    Route::group(['middleware' => ['leader']], function () {
        Route::prefix('group')->group(function () {
            Route::post('/create', CreateGroupController::class);
        });
    });


    Route::controller(GroupInfoController::class)->group(function () {
        Route::prefix('group')->group(function () {
            Route::get('/{id}/info', 'groupInfo');
            Route::get('/{id}/members', 'groupMember');
        });
    });
});

Route::controller(TrainingProgramController::class)->group(function () {
    Route::get('/training-programs', 'trainingPrograms');
});


Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'categories');
});
