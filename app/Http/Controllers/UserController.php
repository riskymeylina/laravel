<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;

class UserController extends Controller
{
    // REGISTER //

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user'    => $user
        ]);
    }

    // LOGIN //
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Token dari Passport
        $token = $user->createToken('UserToken')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user'    => $user,
            'token'   => $token
        ]);
    }

    // PROFILE (HARUS LOGIN) //
    public function me()
    {
        return response()->json([
            'success' => true,
            'user'    => Auth::user()
        ]);
    }

    // LOGOUT (Versi BEBAS ERROR) //
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Revoke semua token user (paling aman & tidak error)
        Token::where('user_id', $user->id)->update(['revoked' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
