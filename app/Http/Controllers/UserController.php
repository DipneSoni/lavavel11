<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function updateProfile(Request $request)
    {
        $userId = Auth::id();
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
        ]);
        $user = User::where('id', $userId)->update($validatedData);
        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user], 200);
    }

    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = Auth::user();

        // Check if the provided old password matches the current password
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                "errors" => [
                    "old_password" => [
                        "The provided old password does not match our records.",
                    ],
                ],
            ], 400);
        }

        // Update the password and hash it
        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ], 200);
    }
}
