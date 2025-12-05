<?php

namespace App\Http\Controllers;

use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServerApiController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        // Получаем ключ из заголовков
        $apiKey = $request->header('X-Authorization-Key');
        $validKey = config('app.api-key');

        // Проверка ключа
        if ($apiKey !== $validKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: invalid API key.'
            ], 401);
        }


        $data = $request->all();
        try{
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'timezone' => 0,
                'google_2fa_enabled' => false,
            ]);
            $user->roles()->attach(3);
            return response()->json([
                'success' => true,
                'message' => 'User created with name:' . $user->name
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }

    }
}
