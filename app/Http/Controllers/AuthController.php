<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * REGISTER USER
     */
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
            'message' => 'Registrasi berhasil',
            'user'    => $user
        ], 201);
    }

    /**
     * LOGIN USER
     */
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

        // Generate token Passport
        $token = $user->createToken('UserToken')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user'    => $user,
            'token'   => $token
        ], 200);
    }

    /**
     * GET USER LOGIN INFO
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user'    => $request->user()
        ]);
    }

    /**
     * LOGOUT USER
     */
    public function logout(Request $request)
    {
        // delete() -> lebih aman & tidak bikin error Intelephense
        $request->user()->token()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
