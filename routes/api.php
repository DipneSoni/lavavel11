<?php

use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [UserController::class, 'register'])->name('register');
Route::post('login', [UserController::class, 'login'])->name('login');

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// Route to send password reset link
Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('sendResetLinkEmail');
// Route to reset password
Route::post('password/reset', [PasswordResetController::class, 'reset'])->name('password.reset');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user(); // Route to get authenticated user info
    });
    Route::post('updateProfile', [UserController::class, 'updateProfile'])->name('updateProfile'); // Route for updateProfile
    Route::post('changePassword', [UserController::class, 'changePassword'])->name('changePassword'); // Route for changePassword
    Route::post('logout', [UserController::class, 'logout'])->name('logout'); // Route for logging out
    Route::put('/posts/{id}', [PostController::class, 'update']); // or use PATCH
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::apiResource('students', StudentController::class); // Protected resource routes for students
});
