<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ======================================================================
    // 1. HALAMAN LOGIN
    // ======================================================================

    /**
     * Menampilkan form login kepada pengguna.
     *
     * @return View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ======================================================================
    // 2. PROSES LOGIN
    // ======================================================================

    /**
     * Memproses data login dengan validasi dan autentikasi.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request)
    {
        // Validasi input dari form login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek kecocokan kredensial dengan database
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // Arahkan ke halaman dashboard penggajian
            return redirect()->intended(route('penggajian.index'));
        }

        // Jika autentikasi gagal, kembalikan ke form dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // ======================================================================
    // 3. PROSES LOGOUT
    // ======================================================================

    /**
     * Memproses logout dengan menghapus sesi pengguna.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        // Logout pengguna
        Auth::logout();

        // Invalidasi session dan regenerate token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman login
        return redirect('/login');
    }
}