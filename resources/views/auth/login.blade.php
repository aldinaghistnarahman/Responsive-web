@extends('layouts.auth')
@section('title', 'Login')

@section('content')
    <h2 style="text-align: center; margin-bottom: 2rem; color: #1f2937;">Selamat Datang</h2>

    @if (session('success'))
        <div class="success-message" role="status">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Masukkan email Anda">
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password" placeholder="Masukkan password Anda">
    <span class="toggle-password" data-target="password">
        <i class="fa-regular fa-eye"></i>
    </span>
    @error('password')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>

        <button type="submit" class="btn-submit">
            <span class="spinner"></span>
            <span class="btn-text">Masuk</span>
        </button>
    </form>

    <div class="auth-links">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </div>
@endsection