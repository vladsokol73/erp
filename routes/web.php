<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CreativesController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\Google2FAController;
use App\Http\Controllers\OperatorsController;
use App\Http\Controllers\ServerApiController;
use App\Http\Controllers\ShorterController;
use App\Http\Controllers\ticket\SettingsController;
use App\Http\Controllers\ticket\TicketController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::view("/", 'home')->name('home')->middleware('auth');

//Auth
Route::view("/login", 'authenticate.login')->name('login')->middleware('guest');
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'loginSubmit')->name('loginSubmit')->middleware('guest');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
    Route::post('/verify-2fa', 'verify2FA')->name('verify2FA')->middleware('guest');
});

// Admin Panel
Route::controller(AdminPanelController::class)->group(function () {
    // Users
    Route::get('/admin-panel/users', 'users')->name('users')->middleware('role:admin');
    Route::post('/admin-panel/users', 'createUser')->name('createUser')->middleware('role:admin');
    Route::post('/admin-panel/users-delete/{id}', 'deleteUser')->name('deleteUser')->middleware('role:admin');
    Route::post('/admin-panel/users-update/{id}', 'editUser')->name('editUser')->middleware('role:admin');
    Route::post('/admin-panel/users-create', 'createUser')->name('createUser')->middleware('role:admin');
    Route::post('/admin-panel/users/{id}/disable-2fa', 'disable2FA')->name('adminDisable2FA')->middleware('role:admin');
    Route::post('/admin-panel/users/{id}/toggle-2fa', [AdminPanelController::class, 'toggle2FA'])->name('toggle2FA')->middleware('role:admin');

    // Operators
    Route::get('admin-panel/operators', 'operators')->name('operators')->middleware('role:admin');
    Route::post('/admin-panel/operators{id}', 'updateOperator')->name('updateOperator')->middleware('role:admin');
    Route::post('/admin-panel/operators-delete/{id}', 'deleteOperator')->name('deleteOperator')->middleware('role:admin');
    Route::post('/admin-panel/channel{id}', 'updateChannel')->name('updateChannel')->middleware('role:admin');
    Route::post('/admin-panel/channels-delete/{id}', 'deleteChannel')->name('deleteChannel')->middleware('role:admin');

    //Tokens
    Route::get('/admin-panel/api-tokens', 'apiTokens')->name('apiTokens')->middleware('role:admin');
    Route::post('/admin-panel/api-tokens', 'createApiToken')->name('createApiToken')->middleware('role:admin');
    Route::post('/admin-panel/edit-api-token/{id}', 'editApiToken')->name('editApiToken')->middleware('role:admin');
    Route::post('/admin-panel/delete-api-token/{id}', 'deleteApiToken')->name('deleteApiToken')->middleware('role:admin');
    Route::get('/admin-panel/api-refresh-operator-names/', 'refreshOperatorsNames')->name('refreshOperatorsNames')->middleware('role:admin');
})->middleware('auth');

// Creatives
Route::controller(CreativesController::class)->group(function () {
    Route::get('/creatives/library', 'index')->name('creatives')->middleware('role:admin,manager,buyer');
    Route::get('/creatives/create', 'create')->name('createCreate')->middleware('permission:creatives.create');
    Route::post('/creatives/create', 'createSubmit')->name('creativeSubmit')->middleware('role:admin,manager,buyer');
    Route::get('/creatives/tags', 'tags')->name('creativesTags')->middleware('permission:creatives.tags');
    Route::post('/creatives/tags', 'createTag')->name('createTag')->middleware('permission:creatives.tags');
    Route::post('/creatives/update-tag/{id}', 'updateTag')->name('updateTag')->middleware('permission:creatives.tags');
    Route::post('/creatives/tags-delete/{id}', 'deleteTag')->name('deleteTag')->middleware('permission:creatives.tags');
    Route::post('/creatives/update{id}', 'updateCreative')->name('updateCreative')->middleware('permission:creatives.update');
    Route::post('/creatives/add-comment/{id}', 'addComment')->name('addCommentToCreative')->middleware('role:admin,manager,buyer');
    Route::post('/creatives/upload-file', 'uploadFile')->name('uploadCreativeFile')->middleware('role:admin,manager,buyer');
    Route::post('/creatives/like', 'setLike')->name('setLike')->middleware('role:admin,manager,buyer');
    Route::post('/creatives/favorite', 'toggleFavorite')->name('favorite')->middleware('role:admin,manager,buyer');
    Route::get('/creatives/favorites', 'favorites')->name('favorites')->middleware('role:admin,manager,buyer');
    Route::post('/creatives/delete{id}', 'deleteCreative')->name('deleteCreative')->middleware('permission:creatives.update');
    Route::post('/creative/delete-file', [CreativesController::class, 'deleteFile'])->name('deleteCreativeFile');
})->middleware('auth');

// Future
Route::controller(FinanceController::class)->group(function () {
    Route::get('/finance/report', 'report')->name('financeReport')->middleware('role:admin');
});

//Скачивание видео/фото
Route::get('/download', [DownloadController::class, 'download'])->name('download')->middleware('auth');

// Clients
Route::controller(ClientsController::class)->group(function () {
    Route::get('/clients', 'index')->name('clients')->middleware('permission:clients.show');
    Route::get('/clients/failed-jobs', 'jobs')->name('jobs')->middleware('permission:clients.show');
    Route::get('/clients/{id}/details', 'getClientDetails')->name('clients.details')->middleware('permission:clients.show');
})->middleware('auth');

Route::post('/jobs/restart-failed', function () {
    Artisan::call('queue:retry all'); // Команда для перезапуска всех failed jobs
    return redirect()->back()->with('status', 'Все failed jobs были перезапущены!');
})->name('jobs.restart')->middleware('permission:clients');

Route::post('/jobs/restart/{uuid}', function ($uuid) {
    Artisan::call('queue:retry', ['id' => $uuid]); // Перезапускаем задачу с определенным ID
    return redirect()->back()->with('status', "Задача с ID $uuid была перезапущена.");
})->name('jobs.restart.single')->middleware('permission:clients');

// Operators
Route::controller(OperatorsController::class)->group(function () {
    Route::get('/operators/statistic', 'statistic')->name('operators.statistic')->middleware('permission:operators.show');
    Route::get('/operators/statistic/export', 'exportStatistic')->name('operators.statistic.export')->middleware('permission:operators.show');
    Route::get('/operators/dashboard', 'dashboard')->name('operators.dashboard')->middleware('permission:operators.show');
})->middleware('auth');

// Shorter
Route::controller(ShorterController::class)->group(function () {
    Route::get('/shorter', 'index')->name('shorter')->middleware('permission:shorter.show');
    Route::get('/shorter/create', 'create')->name('shorterCreate')->middleware('permission:shorter.show');
    Route::post('/shorter/create', 'createSubmit')->name('shorterSubmit')->middleware('permission:shorter.show');
    Route::get('/shorter/domains', 'domains')->name('shorterDomains')->middleware('role:admin');
    Route::get('/shorter/domain-list', 'domainList')->name('shorterDomainList')->middleware('role:admin');
    Route::post('/shorter/domains', 'createDomain')->name('createDomain')->middleware('role:admin');
    Route::put('/shorter/domains/{id}', 'updateDomain')->name('updateDomain')->middleware('role:admin');
    Route::delete('/shorter/domains/{id}', 'deleteDomain')->name('deleteDomain')->middleware('role:admin');
    Route::post('/shorter/edit/{id}', 'editUrl')->name('editUrl')->middleware('permission:shorter.show');
    Route::post('/shorter/delete/{id}', 'deleteUrl')->name('deleteUrl')->middleware('permission:shorter.show');
    Route::post('/shorter/domains/edit/{id}', 'editDomain')->name('editDomain')->middleware('role:admin');
    Route::post('/shorter/domains/delete/{id}', 'deleteDomain')->name('deleteDomain')->middleware('role:admin');
})->middleware('auth');

// Account
Route::controller(AccountController::class)->group(function () {
    Route::get('/account/settings', 'settings')->name('accountSettings');
    Route::post('/account/reset-password', 'resetPassword')->name('accountPasswordReset');
    Route::post('/account/2fa/enable', 'enable2FA')->name('account2FAEnable');
    Route::post('/account/2fa/disable', 'disable2FA')->name('account2FADisable');
    Route::get('/2fa/generate-secret', 'generateSecret')->name('generate2FASecret');
    Route::post('/2fa/confirm', 'confirm2FA')->name('confirm2FA');
    Route::get('/get-telegram-link', 'getTgLink')->name('getTgLink');
    Route::delete('/telegram/destroy', 'destroyTelegram')->name('destroyTelegram');
})->middleware('auth');

// 2FA routes
Route::middleware(['auth'])->group(function () {
    Route::get('2fa/generate-secret', [Google2FAController::class, 'generateSecret'])->name('2fa.generate');
    Route::post('2fa/confirm', [Google2FAController::class, 'confirm'])->name('2fa.confirm');
    Route::post('2fa/disable', [Google2FAController::class, 'disable'])->name('2fa.disable');
    Route::post('2fa/validate', [Google2FAController::class, 'validate'])->name('2fa.validate');
})->middleware('auth');

// Tickets
Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('settings', [SettingsController::class, 'settings'])->name('settings')->middleware('role:admin');

    // Categories
    Route::post('categories', [SettingsController::class, 'storeCategory'])->name('categories.store')->middleware('role:admin');
    Route::put('categories/{category}', [SettingsController::class, 'updateCategory'])->name('categories.update')->middleware('role:admin');
    Route::delete('categories/{category}', [SettingsController::class, 'destroyCategory'])->name('categories.destroy')->middleware('role:admin');

    // Topics
    Route::post('topics', [SettingsController::class, 'storeTopic'])->name('topics.store')->middleware('role:admin');
    Route::put('topics/{topic}', [SettingsController::class, 'updateTopic'])->name('topics.update')->middleware('role:admin');
    Route::delete('topics/{topic}', [SettingsController::class, 'destroyTopic'])->name('topics.destroy')->middleware('role:admin');

    // Statuses
    Route::post('statuses', [SettingsController::class, 'storeStatus'])->name('statuses.store')->middleware('role:admin');
    Route::put('statuses/{status}', [SettingsController::class, 'updateStatus'])->name('statuses.update')->middleware('role:admin');
    Route::delete('statuses/{status}', [SettingsController::class, 'destroyStatus'])->name('statuses.destroy')->middleware('role:admin');

    // Ticket
    Route::get('create', [TicketController::class, 'index'])->name('create');
    Route::post('create', [TicketController::class, 'store'])->name('createSubmit');
    Route::get('/', [TicketController::class, 'show'])->name('show');
    Route::get('/moderation', [TicketController::class, 'moderation'])->name('moderation')->middleware('permission:tickets.moderation');
    Route::get('/all', [TicketController::class, 'showAll'])->name('all')->middleware('role:admin');
    Route::post('/approve', [TicketController::class, 'approve'])->name('approve');
    Route::post('/comment/{ticket}', [TicketController::class, 'comment'])->name('comment');
    Route::post('/comments/update/{commentId}', [TicketController::class, 'updateComment'])->name('updateComment');
    Route::delete('/comments/{commentId}', [TicketController::class, 'destroyComment'])->name('destroyComment');
    Route::post('/update/{ticket}', [TicketController::class, 'update'])->name('update')->middleware('permission:tickets.moderation');
    Route::get('/load-more', [TicketController::class, 'loadMore'])->name('load-more');
    Route::post('/admin-update/{ticket}', [TicketController::class, 'adminUpdate'])->name('admin.update')->middleware('role:admin');
    Route::get('/{ticket}', [TicketController::class, 'showPartial'])->name('showPartial');
    Route::get('/moderation/{ticket}', [TicketController::class, 'moderationPartial'])->name('moderationPartial');
    Route::get('/all/{ticket}', [TicketController::class, 'showAllPartial'])->name('allPartial')->middleware('role:admin');
})->middleware('auth');

// Read notifications
Route::post('/notifications/read', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return response()->json(['status' => 'success']);
})->name('notifications.read')->middleware('auth');

Route::post('/set-timezone', [ServerApiController::class, 'setTimezone'])->middleware('auth');
Route::get('/time', [ServerApiController::class, 'time'])->middleware('auth');
