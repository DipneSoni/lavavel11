<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\CustomResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Send the custom password reset notification
            $token = Password::createToken($user);
            $user->notify(new CustomResetPassword($token, $user->email));

            return response()->json([
                'message' => 'If your email is registered with our site, a password reset link will be sent to you shortly.',
            ], 200);
        }

        return response()->json([
            'message' => 'If your email is registered with our site, a password reset link will be sent to you shortly.',
        ], 200);
    }

    public function reset(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        // Attempt to reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Reset the password
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        // Return appropriate response based on the status
        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully.'], 200);
        } else {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }
}
