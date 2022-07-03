<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|unique:users,phone',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required',
            'password' => 'required|string'
        ]);

        // ckeck phone
        $user = User::where('phone', $data['phone'])->first();

        //check password
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response([
                'message' => 'Bad login!'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'messege' => 'Logged out'
        ];
    }
}
