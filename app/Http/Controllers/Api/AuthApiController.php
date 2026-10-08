<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Simple token (tanpa Sanctum package — token di-remember_token + custom)
        $token = bin2hex(random_bytes(32));
        $user->forceFill(['remember_token' => hash('sha256', $token)])->save();

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'branch_id' => $user->branch_id,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->attributes->get('api_user');
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'branch_id' => $user->branch_id,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->attributes->get('api_user');
        $user->forceFill(['remember_token' => null])->save();
        return response()->json(['message' => 'Logged out']);
    }
}
