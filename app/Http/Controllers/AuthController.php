<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:30|min:4|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4|confirmed',
            'password_confirmation' => 'required|min:4'
        ]);

        User::create($request->only('name','email','password'));

        return response()->json([
            "message" => "User created successfully",
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        return response()->json();
    }
}
