<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CsrfCookieController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Set XSRF-TOKEN cookie via framework helper
        // In Laravel full stack, /sanctum/csrf-cookie handles this.
        $token = csrf_token();

        return response()->json(['ok' => true])->withCookie(cookie(
            'XSRF-TOKEN',
            $token,
            120,
            '/',
            null,
            false,
            false,
            false,
            'lax'
        ));
    }
}