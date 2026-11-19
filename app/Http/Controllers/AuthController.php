<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        return view('auth.login');
    }
    
    public function register(Request $request)
    {
        // Menggunakan validate() untuk Request biasa
        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 1. Cari data mahasiswa berdasarkan NIM dan Email di tabel mahasiswas
        $mahasiswa = Mahasiswa::where('email', $data['email']) ->where('nama', $data['nama'])
                              ->first();
        // 2. Validasi apakah mahasiswa ada dan belum memiliki password
        if ($mahasiswa) {
            return back()->withErrors([
                'nim' => 'Data tidak cocok atau akun sudah aktif.',
            ])->withInput($request->only('nim', 'email', 'nama'));
        }

        // 3. Update password di tabel mahasiswas (aktivasi akun mahasiswa)
       $enkrip = Hash::make($data['password']);

        // 4. Buat akun di tabel users agar bisa login via Auth Laravel
        $user = User::create([
            'name'     => $data['nama'],
            'email'    => $data['email'],
            'password' =>$enkrip
        ]);

        // 5. Otomatis login setelah registrasi dan arahkan ke halaman utama
        Auth::login($user);

        return redirect()->route('login')->with('success', 'Akun aktif dan registrasi berhasil. Silakan login.');
    }

    public function login(LoginRequest $request)
    {
        // Menggunakan LoginRequest yang sudah Anda import
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended('/mahasiswa');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
