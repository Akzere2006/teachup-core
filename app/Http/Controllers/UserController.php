<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('phone', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $user->createToken('authToken')->plainTextToken,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $this->userService->register($request->all());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->only(['first_name', 'last_name', 'phone']);
        $updatedUser = $this->userService->updateProfile($user->id, $data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $updatedUser,
        ]);
    }

    public function getProfile()
    {
        $user = Auth::user();

        return response()->json([
            'user' => $user,
        ]);
    }
}

