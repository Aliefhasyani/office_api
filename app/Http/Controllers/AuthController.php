<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = DB::transaction(function () use ($request) {
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'employee',
            ]);

            Employee::create([
                'user_id' => $newUser->id,
                'employee_name' => $newUser->name,
                'employee_number' => 'EMP-' . str_pad($newUser->id, 4, '0', STR_PAD_LEFT),
            ]);

            return $newUser;
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = DB::transaction(function () use ($socialUser, $provider) {
                $newUser = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password' => null, 
                    'role' => 'employee',
                    'email_verified_at' => now(), 
                ]);

                Employee::create([
                    'user_id' => $newUser->id,
                    'employee_name' => $newUser->name,
                    'employee_number' => 'EMP-' . str_pad($newUser->id, 4, '0', STR_PAD_LEFT),
                ]);

                return $newUser;
            });
        } else {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
}
