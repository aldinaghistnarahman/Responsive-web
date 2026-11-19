
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\AuthController;

// Halaman utama
Route::get('/', function () {
    return redirect()->route('mahasiswa.index');
});

// Guest: belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);
});

// Auth: sudah login
Route::middleware('auth')->group(function () {

    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])
        ->name('mahasiswa.index');

    Route::post('/mahasiswa', [MahasiswaController::class, 'store']);

    Route::get('/mahasiswa/{mahasiswa}/edit', [MahasiswaController::class, 'edit']);

    Route::put('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'update']);

    Route::delete('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
