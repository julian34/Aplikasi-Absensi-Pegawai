<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login with email or NIP (11 digits)
     */
    public function login(Request $request)
    {
        $loginInput = $request->input('login');
        $password = $request->input('password');

        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Determine if input is NIP (11 digits) or email
        $isNip = preg_match('/^\d{11}$/', $loginInput);

        if ($isNip) {
            // Login via NIP
            $pegawai = Pegawai::where('nip', $loginInput)->first();

            if (!$pegawai) {
                throw ValidationException::withMessages([
                    'login' => 'NIP tidak ditemukan.',
                ]);
            }

            $user = $pegawai->user;

            if (!$user || !Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => 'Password salah.',
                ]);
            }

        } else {
            // Login via email
            $user = User::where('email', $loginInput)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'login' => 'Email atau password salah.',
                ]);
            }
        }

        // Buat token akses untuk user yang berhasil logins
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user->load('pegawai'),
        ], 200);
    }

    /**
     * Get current authenticated user with pegawai data
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('pegawai'),
        ], 200);
    }

    /**
     * Logout and clear session
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ], 200);
    }
}
