<?php

use App\Http\Controllers\ServerApiController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/pb', [WebhookController::class, 'puzzleBot']);
Route::post('/webhook/c2d', [WebhookController::class, 'chat2Desk']);
Route::post('/c2d/operator', [WebhookController::class, 'c2dOperators']);
Route::get('/webhook/product', [WebhookController::class, 'product']);
Route::post('/webhook/tg-bot', [WebhookController::class, 'tgBot']);
Route::post('/auth/registration', [ServerApiController::class, 'register']);
