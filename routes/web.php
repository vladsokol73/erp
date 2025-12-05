<?php

use App\Http\Controllers\Admin\AiRetentionController;
use App\Http\Controllers\ForcedPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\ApiTokenController;
use App\Http\Controllers\Admin\OperatorController as AdminOperatorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Creative\CreativeController;
use App\Http\Controllers\Creative\TagController;
use App\Http\Controllers\FailedJobController;
use App\Http\Controllers\Google2FAController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\Ticket\CheckPlayerController;
use App\Http\Controllers\Ticket\ProductLogController;
use App\Http\Controllers\Ticket\TicketsController;
use App\Http\Controllers\ShorterController;
use App\Http\Controllers\Ticket\TicketSettingsController;
use App\Http\Controllers\MeetRedirectController;
use App\Http\Controllers\MeetController;
use App\Http\Controllers\GChatSsoController;
use Illuminate\Support\Facades\Route;

// ----- Публичные маршруты -----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/login/2fa', [AuthController::class, 'verify2FA'])->name('2fa.verify');
});

// ----- Приватные маршруты (auth) -----
Route::middleware(['auth'])->group(function () {
    Route::get('/force-password-change', [ForcedPasswordController::class, 'show'])->name('password.force.show');
    Route::post('/force-password-change', [ForcedPasswordController::class, 'update'])->name('password.force.update');
});

// ----- Приватные маршруты (auth + force.password) -----
Route::middleware(['auth', 'force.password'])->group(function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Account
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/settings', [AccountController::class, 'showSettings'])->name('settings.show');
        Route::put('/reset-password', [AccountController::class, 'resetPassword'])->name('password.reset');
        Route::get('/get-telegram-link', [AccountController::class, 'getTgLink'])->name('telegram.link');
        Route::delete('/telegram/destroy', [AccountController::class, 'destroyTelegram'])->name('telegram.destroy');
        Route::get('/telegram/check', [AccountController::class, 'checkTelegram'])->name('telegram.check');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'showNotifications'])->name('show');
        Route::post('/read-one', [NotificationController::class, 'markOneAsRead'])->name('read-one');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    });

    // Creatives
    Route::prefix('creatives')->name('creatives.')->group(function () {
        Route::get('/library', [CreativeController::class, 'showLibrary'])->name('library.show');
        Route::delete('/library/{creativeId}', [CreativeController::class, 'deleteCreative'])->name('library.delete');
        Route::post('/library/{creativeId}/reactions', [CreativeController::class, 'reactionsCreative'])->name('library.reactions');
        Route::post('/library/{creativeId}/favorites', [CreativeController::class, 'favoritesCreative'])->name('library.favorites');
        Route::post('/library/{creativeId}/comments', [CreativeController::class, 'commentsCreative'])->name('library.comments');
        Route::put('/library/{creativeId}/tags', [CreativeController::class, 'updateTags'])->name('library.tags.update');

        Route::get('/new-creative', [CreativeController::class, 'showNewCreative'])->name('new_creative.show');
        Route::post('/new-creative', [CreativeController::class, 'createCreative'])->name('new_creative.create');
        Route::post('/new-creative/upload', [CreativeController::class, 'uploadFile'])->name('new_creative.upload');
        Route::post('/new-creative/delete', [CreativeController::class, 'deleteFile'])->name('new_creative.delete');

        Route::get('/tags', [TagController::class, 'showTags'])->name('tags.show');
        Route::post('/tags', [TagController::class, 'createTag'])->name('tags.create');
        Route::put('/tags/{tagId}', [TagController::class, 'updateTag'])->name('tags.update');
        Route::delete('/tags/{tagId}', [TagController::class, 'deleteTag'])->name('tags.delete');

        Route::get('/favorites', [CreativeController::class, 'showFavorites'])->name('favorites.show');
    });

    // Operators
    Route::prefix('operators')->name('operators.')->group(function () {
        Route::get('/statistic', [OperatorController::class, 'showStatistic'])->name('statistic.show');
        Route::get('/{operatorId}/reports', [OperatorController::class, 'showReports'])->name('reports.show');
        Route::get('/dashboard', [OperatorController::class, 'showDashboard'])->name('dashboard.show');
    });

    // Clients
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'showAllClients'])->name('all.clients.show');
        Route::get('/details/{clientId}', [ClientController::class, 'showClientDetails'])->name('details.show');
        Route::get('/logs/{clientId}', [ClientController::class, 'showClientLogs'])->name('logs.show');
        Route::get('/failed-jobs', [FailedJobController::class, 'showFailedJobs'])->name('failed.jobs.show');
        Route::post('/failed-jobs/{failedJobId}/restart', [FailedJobController::class, 'restartJob'])->name('failed.jobs.restart');
        Route::post('/failed-jobs/restart-all', [FailedJobController::class, 'restartAll'])->name('failed.jobs.restart.all');
    });

    // Shorter
    Route::prefix('shorter')->name('shorter.')->group(function () {
        Route::get('/create', [ShorterController::class, 'showShortUrl'])->name('show');
        Route::post('/create', [ShorterController::class, 'createShortUrl'])->name('url.create');
        Route::get('/', [ShorterController::class, 'showManagerUrl'])->name('url.show');
        Route::put('/{urlId}', [ShorterController::class, 'editUrl'])->name('url.edit');
        Route::delete('/{urlId}', [ShorterController::class, 'deleteUrl'])->name('url.delete');
        Route::get('/domains', [ShorterController::class, 'showDomains'])->name('domains.show');
        Route::post('/domains', [ShorterController::class, 'createDomain'])->name('domain.create');
        Route::put('/domains/{domainId}', [ShorterController::class, 'editDomain'])->name('domains.edit');
        Route::delete('/domains/{domainId}', [ShorterController::class, 'deleteDomain'])->name('domains.delete');
    });

    // 2FA
    Route::prefix('2fa')->name('2fa.')->group(function () {
        Route::get('/generate-secret', [Google2FAController::class, 'generateSecret'])->name('generate');
        Route::post('/confirm', [Google2FAController::class, 'confirm'])->name('confirm');
        Route::post('/disable', [Google2FAController::class, 'disable'])->name('disable');
        Route::post('/validate', [Google2FAController::class, 'verify'])->name('validate');
    });

    // Tickets
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/create', [TicketsController::class, 'showCreate'])->name('create.show');
        Route::post('/create', [TicketsController::class, 'createTicket'])->name('create');
        Route::get('/', [TicketsController::class, 'showMy'])->name('my.show');
        Route::get('/more', [TicketsController::class, 'getMyMore'])->name('my.show.more');
        Route::get('/moderation', [TicketsController::class, 'showModeration'])->name('moderation.show');
        Route::get('/moderation/more', [TicketsController::class, 'getModerationMore'])->name('moderation.show.more');
        Route::get('/all', [TicketsController::class, 'showAll'])->name('all.show');
        Route::get('/all/more', [TicketsController::class, 'getAllMore'])->name('all.show.more');
        Route::post('/{ticketId}/comments', [TicketsController::class, 'commentsTicket'])->name('comments');
        Route::post('/{ticketId}/approve', [TicketsController::class, 'approveTicket'])->name('approve');
        Route::put('/{ticketId}/update/owner', [TicketsController::class, 'updateOwnerTicket'])->name('updateOwner');
        Route::put('/{ticketId}/update/moderator', [TicketsController::class, 'updateModeratorTicket'])->name('updateModerator');
        Route::put('{ticketId}/update/admin', [TicketsController::class, 'updateAdminTicket'])->name('updateAdmin');
        Route::delete('/{ticketId}/delete', [TicketsController::class, 'deleteTicket'])->name('delete');

        // Settings
        Route::get('/settings/categories', [TicketSettingsController::class, 'showSettingsCategory'])->name('settings.categories.show');
        Route::post('/settings/categories', [TicketSettingsController::class, 'createCategory'])->name('settings.categories.create');
        Route::put('/settings/categories/{categoryId}', [TicketSettingsController::class, 'updateCategory'])->name('settings.categories.update');
        Route::delete('/settings/categories/{categoryId}', [TicketSettingsController::class, 'deleteCategory'])->name('settings.categories.delete');

        Route::get('/settings/topic', [TicketSettingsController::class, 'showSettingsTopic'])->name('settings.topic.show');
        Route::post('/settings/topic', [TicketSettingsController::class, 'createTopic'])->name('settings.topic.create');
        Route::put('/settings/topic/{topicId}', [TicketSettingsController::class, 'updateTopic'])->name('settings.topic.update');
        Route::delete('/settings/topic/{topicId}', [TicketSettingsController::class, 'deleteTopic'])->name('settings.topic.delete');

        Route::get('/settings/statuses', [TicketSettingsController::class, 'showSettingsStatuses'])->name('settings.statuses.show');
        Route::post('/settings/statuses', [TicketSettingsController::class, 'createStatuses'])->name('settings.statuses.create');
        Route::put('/settings/statuses/{statusId}', [TicketSettingsController::class, 'updateStatus'])->name('settings.statuses.update');
        Route::delete('/settings/statuses/{statusId}', [TicketSettingsController::class, 'deleteStatus'])->name('settings.statuses.delete');

        Route::get('/settings/fields', [TicketSettingsController::class, 'showSettingsFields'])->name('settings.fields.show');
        Route::post('/settings/fields', [TicketSettingsController::class, 'createFields'])->name('settings.fields.create');
        Route::put('/settings/fields/{fieldsId}', [TicketSettingsController::class, 'updateFields'])->name('settings.fields.update');
        Route::delete('/settings/fields/{fieldsId}', [TicketSettingsController::class, 'deleteFields'])->name('settings.fields.delete');

        // Check Player
        Route::get('/player/my', [CheckPlayerController::class, 'showMy'])->name('player.show');
        Route::get('/player/my/more', [CheckPlayerController::class, 'getMyMore'])->name('player.show.more');

        Route::get('/player/create', [CheckPlayerController::class, 'showCreateTicket'])->name('player.create.show');
        Route::post('/player/create', [CheckPlayerController::class, 'createTicket'])->name('player.create');

        Route::get('/player/moderation', [CheckPlayerController::class, 'showModeration'])->name('player.show.moderation');
        Route::get('/player/moderation/more', [CheckPlayerController::class, 'getModerationMore'])->name('player.show.moderation.more');
        Route::put('/player/{ticketId}/moderation', [CheckPlayerController::class, 'updateTicket'])->name('player.update');

        Route::post('/player/{ticketId}/comments', [CheckPlayerController::class, 'commentsPlayer'])->name('player.comments');

        // Check Player - Product Logs
        Route::get('/player/logs', [ProductLogController::class, 'showLogs'])->name('player.logs.show');
    });

    // Meetings (Jitsi JWT redirect)
    Route::get('/meet', [MeetController::class, 'showMeet'])->name('meet.show');
    Route::get('/meet/{room?}', [MeetRedirectController::class, 'go'])
        ->where('room', '[A-Za-z0-9_-]+')
        ->name('meet.redirect');

    Route::get('/meet/guest/{room?}', [MeetRedirectController::class, 'guest'])
        ->where('room', '[A-Za-z0-9_-]+')
        ->name('meet.public.redirect');

    // Meetings API (ApiResponse)
    Route::post('/meet/room', [MeetController::class, 'generateRoom'])->name('meet.room.generate');
    Route::get('/meet/link/{room}', [MeetController::class, 'joinLink'])
        ->where('room', '[A-Za-z0-9_-]+')
        ->name('meet.link');

    // Admin Panel
    Route::prefix('admin-panel')->name('admin-panel.')->group(function () {
        Route::get('/users', [UserController::class, 'showUsers'])->name('users.show');
        Route::post('/users', [UserController::class, 'createUser'])->name('users.create');
        Route::put('/users/{userId}', [UserController::class, 'editUser'])->name('users.edit');
        Route::delete('/users/{userId}', [UserController::class, 'deleteUser'])->name('users.delete');

        Route::get('/operators', [AdminOperatorController::class, 'showOperators'])->name('operators.show');
        Route::put('/operators/{operatorId}', [AdminOperatorController::class, 'editOperator'])->name('operators.edit');
        Route::delete('/operators/{operatorId}', [AdminOperatorController::class, 'deleteOperator'])->name('operators.delete');
        Route::put('/operators/channels/{channelId}', [AdminOperatorController::class, 'editChannel'])->name('operators.channels.edit');
        Route::delete('/operators/channels/{channelId}', [AdminOperatorController::class, 'deleteChannel'])->name('operators.channels.delete');

        Route::put('/ai-reports', [AiRetentionController::class, 'editPrompt'])->name('ai-reports.edit-prompt');
        Route::get('/ai-reports/test', [AiRetentionController::class, 'testPromptReport'])->name('ai-reports.test');
        Route::get('/ai-reports', [AiRetentionController::class, 'showTestReports'])->name('ai-reports.show');
        Route::get('/ai-reports/process-job', [AiRetentionController::class, 'processJobReport'])->name('ai-reports.process-job');

        Route::get('/api-tokens', [ApiTokenController::class, 'showApiTokens'])->name('api.tokens.show');
        Route::post('/api-tokens', [ApiTokenController::class, 'createApiToken'])->name('api.tokens.create');
        Route::put('api-tokens/{apiTokenId}', [ApiTokenController::class, 'editApiToken'])->name('api.tokens.edit');
        Route::delete('api-tokens/{apiTokenId}', [ApiTokenController::class, 'deleteApiToken'])->name('api.token.delete');
    });
});

// GChat SSO endpoint (public, handles guest redirect)
Route::get('/gchat/sso', [GChatSsoController::class, 'index'])->name('gchat.sso');
