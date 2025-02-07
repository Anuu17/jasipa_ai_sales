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

//チャット画面関連
Route::group(['middleware'=>['auth', 'verified','check_role:Company admin|General user']],function () {
    Route::post('/chat', [ChatController::class, 'chat']);
    Route::post('/api/chat', [ChatController::class, 'chat']);
    Route::get('chat_screen/chat', [ChatController::class, 'chat_screen'])->name('chat_screen.chat');
    Route::get('/getTokenCount',[ChatController::class,'getTokenCount']);
    Route::post('chat_screen/prompt',[ChatController::class,'prompt_save'])->name('chat_screen.prompt.save');
    Route::get('chat_screen/prompt/reset', [ChatController::class, 'prompt_reset'])->name('chat_screen.prompt.reset');
    Route::post('/api/chat/new_message', [ChatController::class, 'newMessage']);
    Route::get('/conversation/{id}/details', [ChatController::class, 'getDetails'])->name('conversation.details');
    Route::delete('chat_screen/conversations/{conversation}', [ChatController::class, 'destroy'])->name('chat_screen.conversations.destroy');
    Route::put('/conversation/update/{id}', [ChatController::class, 'updateLabel']);


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

});



require __DIR__.'/auth.php';

require __DIR__.'/admin.php';
