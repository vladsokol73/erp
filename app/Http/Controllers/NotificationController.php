<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkNotificationAsReadRequest;
use App\Http\Responses\ApiResponse;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function showNotifications(): JsonResponse
    {
        $notifications = $this->notificationService->getUserNotifications();

        return ApiResponse::success([
            'notifications' => $notifications,
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        $this->notificationService->markAllAsRead();

        return ApiResponse::successMessage('All notifications marked as read.');
    }

    public function markOneAsRead(MarkNotificationAsReadRequest $request): JsonResponse
    {
        $this->notificationService->markAsReadById($request->id);

        return ApiResponse::successMessage( 'Notification marked as read.');
    }
}
