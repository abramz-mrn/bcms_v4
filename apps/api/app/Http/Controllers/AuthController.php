<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        $request->session()->regenerate();

        $user = $request->user();

        AuditLog::query()->create([
            'users_id' => $user->id,
            'users_name' => $user->name,
            'ip_address' => $request->ip(),
            'action' => 'login',
            'resource_type' => 'auth',
            'old_value' => null,
            'new_value' => [
                'status' => 200,
                'email' => $user->email,
            ],
            'description' => 'User login',
        ]);

        return response()->json([
            'message' => 'Logged in',
            'user' => $user->load('group','company'),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('group','company'),
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        AuditLog::query()->create([
            'users_id' => $user?->id,
            'users_name' => $user?->name,
            'ip_address' => $request->ip(),
            'action' => 'logout',
            'resource_type' => 'auth',
            'old_value' => null,
            'new_value' => [
                'status' => 200,
            ],
            'description' => 'User logout',
        ]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }
}