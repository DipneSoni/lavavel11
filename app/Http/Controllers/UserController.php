<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = User::create($validatedData);
        $user['token'] = $user->createToken($user->email)->plainTextToken;
        return response()->json([
            'message' => 'Successfully register',
            'user' => $user], 200);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            $err = ["errors" => ["email" => ["Invalid credentials."]]];
            return response()->json($err, 400);
        }

        // Generate a Sanctum token
        $user['token'] = $user->createToken($user->email)->plainTextToken;
        return response()->json([
            'message' => 'Successfully login',
            'user' => $user], 200);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }
}
