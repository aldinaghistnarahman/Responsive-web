@extends('layouts.auth')
@section('title', 'Registrasi')

@section('content')
    <h2 style="text-align: center; margin-bottom: 2rem; color: #1f2937;">Buat Akun</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" maxlength="100" required autocomplete="nama" autofocus placeholder="Masukkan nama lengkap Anda">
@error('nama')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" placeholder="contoh@email.com">
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" placeholder="Minimal 8 karakter, simbol & angka">
    <span class="toggle-password" data-target="password">
        <i class="fa-regular fa-eye"></i>
    </span>
    
    <div class="password-strength">
        <div class="strength-bar" id="strength-bar"></div>
    </div>
    <small id="strength-text" style="font-size: 0.75rem; color: #6b7280; display: block; margin-top: 5px;"></small>

    @error('password')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>


<div class="form-group">
    <label for="password_confirmation">Konfirmasi Password</label>
    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="Ketik ulang password">
    
    <!-- Pastikan data-target di bawah ini adalah "password_confirmation" -->
    <span class="toggle-password" data-target="password_confirmation">
        <i class="fa-regular fa-eye"></i>
    </span>
    
    <!-- Indikator kecocokan password -->
    <small id="match-text" style="font-size: 0.75rem; display: block; margin-top: 5px; transition: all 0.3s;"></small>
</div>

        <button type="submit" class="btn-submit">
            <span class="spinner"></span>
            <span class="btn-text">Daftar Sekarang</span>
        </button >
    </form>

    <div class="auth-links">
        Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
    </div>
@endsection

@stack('scripts')
<script>
    // Password Strength Meter Script
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');

    passwordInput.addEventListener('input', function() {
        const val = this.value;
        let score = 0;
        
        if(val.length >= 8) score++;
        if(/[A-Z]/.test(val)) score++;
        if(/[a-z]/.test(val)) score++;
        if(/[0-9]/.test(val)) score++;
        if(/[^A-Za-z0-9]/.test(val)) score++;

        let color = '#e5e7eb';
        let text = '';
        let width = '0%';

        switch(score) {
            case 1: case 2: 
                width = '30%'; color = '#ef4444'; text = 'Sangat Lemah (Belum memenuhi syarat)'; 
                break;
            case 3: 
                width = '50%'; color = '#f59e0b'; text = 'Lemah (Tambahkan simbol/angka)'; 
                break;
            case 4: 
                width = '80%'; color = '#3b82f6'; text = 'Sedang (Sudah cukup)'; 
                break;
            case 5: 
                width = '100%'; color = '#10b981'; text = 'Kuat (Kombinasi sangat baik)'; 
                break;
        }

        if(val.length === 0) {
            width = '0%'; text = '';
        }

        strengthBar.style.width = width;
        strengthBar.style.backgroundColor = color;
        strengthText.innerText = text;
        strengthText.style.color = color;
    });


    // Script Pengecekan Konfirmasi Password
    const confirmInput = document.getElementById('password_confirmation');
    const matchText = document.getElementById('match-text');

    function checkPasswordMatch() {
        const passVal = passwordInput.value;
        const confVal = confirmInput.value;

        if (confVal.length === 0) {
            matchText.innerText = '';
            confirmInput.style.borderColor = '#e5e7eb'; // Kembali ke warna border default
            return;
        }

        if (passVal === confVal) {
            matchText.innerText = 'Password cocok';
            matchText.style.color = '#10b981'; // Warna Hijau
            confirmInput.style.borderColor = '#10b981'; 
        } else {
            matchText.innerText = 'Password tidak cocok';
            matchText.style.color = '#ef4444'; // Warna Merah
            confirmInput.style.borderColor = '#ef4444';
        }
    }

    // Jalankan fungsi saat pengguna mengetik di kedua input
    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmInput.addEventListener('input', checkPasswordMatch);
</script>