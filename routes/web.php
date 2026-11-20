
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
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
    Route::get('/dashboard', function () {
        return view('app', [
            'page' => [
                'name' => 'dashboard',
                'csrfToken' => csrf_token(),
                'mahasiswaCount' => \App\Models\Mahasiswa::count(),
                'prestasiCount' => \App\Models\Prestasi::count(),
                'recentPrestasis' => \App\Models\Prestasi::with('mahasiswas')
                    ->orderByDesc('tahun')
                    ->latest()
                    ->take(4)
                    ->get()
                    ->map(fn ($prestasi) => [
                        'id' => $prestasi->id,
                        'nama_prestasi' => $prestasi->nama_prestasi,
                        'tingkat' => $prestasi->tingkat,
                        'tahun' => $prestasi->tahun,
                        'mahasiswa' => $prestasi->mahasiswas->pluck('nama'),
                    ]),
            ],
        ]);
    })->name('dashboard');

    Route::resource('mahasiswa', MahasiswaController::class)->only([
        'index', 'store', 'edit', 'update', 'destroy',
    ]);

    Route::resource('prestasi', PrestasiController::class)->except(['show']);

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
