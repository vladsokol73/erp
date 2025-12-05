<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Log;
use App\Services\Meet\MeetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MeetController extends Controller
{
    public function __construct(
        public readonly MeetService $meetService
    )
    {
    }

    // POST /meet/room → создать комнату, вернуть имя и TTL
    public function generateRoom(Request $request): JsonResponse
    {
        try {
            $userId = (int) (Auth::id() ?? 0);
            $room = $userId > 0
                ? $this->meetService->generateRoomForUser($userId)
                : $this->meetService->generateRoom(null);
            return ApiResponse::created([
                'room' => $room,
                'ttl_seconds' => $this->meetService->getRoomTtlSeconds(),
            ]);
        } catch (\Throwable $e) {
            Log::error('MeetController.generateRoom error: '.$e->getMessage());
            return ApiResponse::serverError('Failed to generate room');
        }
    }

    // GET /meet/link/{room} → вернуть ссылку для входа (JWT) если комната существует
    public function joinLink(Request $request, string $room): JsonResponse
    {
        $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '', $room) ?? '';
        if ($sanitized === '') {
            return ApiResponse::validationMessage('Invalid room');
        }

        if (!$this->meetService->roomExists($sanitized)) {
            return ApiResponse::notFound('Room not found or expired');
        }

        $user = Auth::user();
        try {
            $url = $this->meetService->buildJoinUrl($user, $sanitized);
            return ApiResponse::success([
                'join_url' => $url,
            ]);
        } catch (\Throwable $e) {
            Log::error('MeetController.joinLink error: '.$e->getMessage());
            return ApiResponse::serverError('Failed to build join link');
        }
    }

    public function showMeet(): Response
    {
        $user = Auth::user();
        $rooms = $user ? $this->meetService->listUserRooms((int)$user->id) : [];

        return Inertia::render('Meet', [
            'rooms' => $rooms,
            'ttl_seconds' => $this->meetService->getRoomTtlSeconds(),
        ]);
    }

    // GET /meet/rooms → история комнат текущего пользователя
    public function listUserRooms(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return ApiResponse::unauthorized();
        }
        try {
            $rooms = $this->meetService->listUserRooms((int)$user->id);
            return ApiResponse::success([
                'rooms' => $rooms,
            ]);
        } catch (\Throwable $e) {
            Log::error('MeetController.listUserRooms error: '.$e->getMessage());
            return ApiResponse::serverError('Failed to load rooms');
        }
    }
}
