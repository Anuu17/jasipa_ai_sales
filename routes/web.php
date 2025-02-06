<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\CompanyAdminDashboardController;
use App\Http\Controllers\Frontend\UserDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});



//一般ユーザー
Route::group(['middleware'=>['auth', 'verified','check_role:General user']],function () {

    Route::get('general_user/chat', [ChatController::class, 'chat_screen'])->name('general_user.chat');
    Route::post('/chat', [ChatController::class, 'chat']);
    Route::post('general_user/api/chat', [ChatController::class, 'chat']);
    Route::post('general_user/api/chat/new_message', [ChatController::class, 'newMessage']);
    Route::post('chat/file_upload', [ChatController::class, 'file_upload']);
    Route::post('general_user/prompt',[ChatController::class,'prompt_save'])->name('general_user.prompt.save');
    Route::get('general_user/prompt/reset', [ChatController::class, 'prompt_reset'])->name('general_user.prompt.reset');
    Route::get('general_user/conversation/{id}/details', [ChatController::class, 'getDetails'])->name('conversation.details');
    Route::delete('general_user/conversations/{conversation}', [ChatController::class, 'destroy'])->name('general_user.conversations.destroy');
    Route::get('general_user/getTokenCount',[ChatController::class,'getTokenCount']);
});



//法人管理者
Route::group(['middleware'=>['auth', 'verified','check_role:Company admin']],function () {
    Route::get('/company_user', [DashboardController::class, 'company_user_show'])->name('company_user');
    Route::get('company_user/create',[DashboardController::class, 'company_user_create'])->name('company_user.create');
    Route::post('company_user/store', [DashboardController::class, 'company_user_store'])->name('company_users.store');
    Route::get('company_user/{user}/edit',[DashboardController::class, 'company_user_edit'])->name('company_users.edit');
    Route::put('company_user/{user}', [DashboardController::class, 'company_user_update'])->name('company_user.update');
    Route::delete('company_user/{user}', [DashboardController::class, 'company_user_destroy'])->name('company_user.destroy');

    Route::get('/user_token', [DashboardController::class, 'company_user_token_show'])->name('company_user_token');
    Route::post('user_token/store', [DashboardController::class, 'company_user_token_store'])->name('company_user_token.store');

    Route::get('/prompt', [DashboardController::class, 'prompt_show'])->name('prompt');
    Route::post('/prompt/store', [DashboardController::class, 'prompt_store'])->name('prompt.store');

    Route::post('/chat', [ChatController::class, 'chat']);
    Route::post('company_admin/api/chat', [ChatController::class, 'chat']);
    Route::get('company_admin/chat', [ChatController::class, 'chat_screen'])->name('company_admin.chat');
    Route::post('company_admin/prompt',[ChatController::class,'prompt_save'])->name('company_admin.prompt.save');
    Route::get('company_admin/prompt/reset', [ChatController::class, 'prompt_reset'])->name('company_admin.prompt.reset');

    Route::post('company_admin/api/chat/new_message', [ChatController::class, 'newMessage']);
    Route::get('company_admin/conversation/{id}/details', [ChatController::class, 'getDetails'])->name('conversation.details');
    Route::delete('company_admin/conversations/{conversation}', [ChatController::class, 'destroy'])->name('company_admin.conversations.destroy');

    Route::get('company_admin/getTokenCount',[ChatController::class,'getTokenCount']);

});



require __DIR__.'/auth.php';

require __DIR__.'/admin.php';
