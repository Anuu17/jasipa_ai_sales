<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\DashboardController;

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=> "guest:admin", "prefix" => "administrator", "as" => "admin."], function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::group(["middleware" => "auth:admin", "prefix" => "administrator", "as" => "admin."],function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('user_info', [DashboardController::class, 'user_info_show'])->name('user_info');
    Route::get('user_info/create',[DashboardController::class, 'user_info_create'])->name('user_info.create');
    Route::post('user_info/store', [DashboardController::class, 'user_info_store'])->name('user_info.store');
    Route::get('/user_info/{user}/edit', [DashboardController::class, 'user_info_edit'])->name('user_info.edit');
    Route::put('/user_info/{user}', [DashboardController::class, 'user_info_update'])->name('user_info.update');
    Route::delete('/user_info/{user}', [DashboardController::class, 'user_info_destroy'])->name('user_info.destroy');

    Route::get('admin_info', [DashboardController::class, 'admin_info_show'])->name('admin_info');
    Route::get('admin_info/create',[DashboardController::class, 'admin_info_create'])->name('admin_info.create');
    Route::post('admin_info/store', [DashboardController::class, 'admin_info_store'])->name('admin_info.store');
    Route::get('/admin_info/{admin}/edit', [DashboardController::class, 'admin_info_edit'])->name('admin_info.edit');
    Route::put('/admin_info/{admin}', [DashboardController::class, 'admin_info_update'])->name('admin_info.update');
    Route::delete('/admin_info/{admin}', [DashboardController::class, 'admin_info_destroy'])->name('admin_info.destroy');

    Route::get('company_info', [DashboardController::class, 'company_info_show'])->name('company_info');
    Route::get('company_info/create',[DashboardController::class, 'company_info_create'])->name('company_info.create');
    Route::post('company_info/store', [DashboardController::class, 'company_info_store'])->name('company_info.store');
    Route::get('/company_info/{company}/edit', [DashboardController::class, 'company_info_edit'])->name('company_info.edit');
    Route::put('/company_info/{company}', [DashboardController::class, 'company_info_update'])->name('company_info.update');
    Route::delete('/company_info/{company}', [DashboardController::class, 'company_info_destroy'])->name('company_info.destroy');

    Route::get('ai_info', [DashboardController::class, 'ai_info_show'])->name('ai_info');
    Route::get('ai_info/create',[DashboardController::class, 'ai_info_create'])->name('ai_info.create');
    Route::post('ai_info/store', [DashboardController::class, 'ai_info_store'])->name('ai_info.store');
    Route::get('/ai_info/{ai}/edit', [DashboardController::class, 'ai_info_edit'])->name('ai_info.edit');
    Route::put('/ai_info/{ai}', [DashboardController::class, 'ai_info_update'])->name('ai_info.update');
    Route::delete('/ai_info/{ai}', [DashboardController::class, 'ai_info_destroy'])->name('ai_info.destroy');

    Route::get('company_token', [DashboardController::class, 'company_token_show'])->name('company_token');
    Route::post('company_token/store', [DashboardController::class, 'company_token_store'])->name('company_token.store');

    Route::get('company_token/{company}/users', [DashboardController::class, 'users_token_show'])->name('users_token');
    Route::post('company_token/{company}/users/store', [DashboardController::class, 'users_token_store'])->name('users_token.store');


});
