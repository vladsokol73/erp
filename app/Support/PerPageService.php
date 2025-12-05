<?php

namespace App\Support;

use Illuminate\Http\Request;

class PerPageService
{
    public function resolve(Request $request, string $key, int $default): int
    {
        $param = $request->query('perPage');

        if ($param !== null) {
            $value = max(1, (int) $param); // защита от 0/отрицательных
            session()->put("per_page.{$key}", $value);
            return $value;
        }

        return session("per_page.{$key}", $default);
    }
}
