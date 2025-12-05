<?php

use App\Http\Controllers\ServerApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::post('/auth/registration', [ServerApiController::class, 'register']);
Route::post('/webhook/tg-bot', [WebhookController::class, 'tgBot']);
