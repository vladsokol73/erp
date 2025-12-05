<?php

namespace App\Http\Controllers;

use App\Http\Requests\GChatSsoRequest;
use App\Contracts\Security\JwtEncoder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class GChatSsoController extends Controller
{
    public function __construct(private readonly JwtEncoder $jwtService)
    {
    }

    public function index(GChatSsoRequest $request): RedirectResponse
    {
        $redirectUri = $request->get('redirect_uri');
        $state = $request->get('state');

        if (!Auth::check()) {
            $original = url('/gchat/sso') . '?redirect_uri=' . urlencode($redirectUri) . ($state ? '&state=' . urlencode($state) : '');
            return redirect('/login?redirect=' . urlencode($original));
        }

        $user = Auth::user();
        $now = time();
        $ttl = (int) Config::get('gchat.ttl', 3600);

        $payload = [
            'iss' => Config::get('gchat.issuer', 'erp-auth'),
            'aud' => Config::get('gchat.audience', 'gchat'),
            'sub' => (string) $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'iat' => $now,
            'exp' => $now + $ttl,
        ];

        $token = $this->jwtService->encode($payload);

        $hash = '#jwt=' . urlencode($token);
        if ($state !== null) {
            $hash .= '&state=' . urlencode($state);
        }

        return redirect()->away($redirectUri . $hash);
    }
}


