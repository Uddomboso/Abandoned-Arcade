<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// api authentication controller
// handles user registration, login, logout, and user info
// uses laravel sanctum for token based authentication
class AuthController extends Controller
{
    // register new user
    // creates user account and returns authentication token
    public function register(Request $request)
    {
        // validate registration data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // create user with hashed password
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // create sanctum token for api authentication
        $token = $user->createToken('auth-token')->plainTextToken;

        // return user data and token
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    // login user and create token
    // authenticates user credentials and returns token
    public function login(Request $request)
    {
        // validate login credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // find user by email
        $user = User::where('email', $request->email)->first();

        // verify password matches
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // create sanctum token for api authentication
        $token = $user->createToken('auth-token')->plainTextToken;

        // return user data and token
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    // logout user (revoke the token)
    // deletes current access token to invalidate session
    public function logout(Request $request)
    {
        // delete current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    // get authenticated user
    // returns current user information
    public function user(Request $request)
    {
        return new UserResource($request->user());
    }
}
