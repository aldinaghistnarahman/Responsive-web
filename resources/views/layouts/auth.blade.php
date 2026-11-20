<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Aplikasi Mahasiswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --error: #ef4444;
            --bg-gradient-1: #ebf4f5;
            --bg-gradient-2: #b5c6e0;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(-45deg, var(--bg-gradient-1), var(--bg-gradient-2), #f3e7e9, #e3eeff);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            padding: 1rem;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(10px);
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group { margin-bottom: 1.5rem; position: relative; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; font-size: 0.9rem; }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            outline: none;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2); }
        .form-control.is-invalid { border-color: var(--error); animation: shake 0.4s ease-in-out; }
        .form-control.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2); }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
        }

        .error-message {
            color: var(--error);
            font-size: 0.85rem;
            margin-top: 0.4rem;
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .success-message {
            color: #087f5b;
            font-size: 0.9rem;
            line-height: 1.5;
            margin: -1rem 0 1.5rem;
            padding: 0.8rem 1rem;
            border: 1px solid #a7e4c8;
            border-radius: 0.65rem;
            background: linear-gradient(120deg, #edfff6, #eafaff);
            animation: slideUpFade 0.4s ease both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-submit:hover { background-color: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .btn-submit:active { transform: translateY(0); }

       .toggle-password {
    position: absolute;
    right: 15px;
    top: 36px; /* Disesuaikan agar ikon sejajar */
    cursor: pointer;
    color: #6b7280;
    font-size: 1.1rem; /* Ukuran ikon */
    user-select: none;
    transition: color 0.3s ease;
}
.toggle-password:hover {
    color: var(--primary);
}

        .password-strength { height: 4px; border-radius: 2px; background: #e5e7eb; margin-top: 8px; overflow: hidden; transition: all 0.3s; }
        .strength-bar { height: 100%; width: 0; transition: width 0.4s ease, background-color 0.4s ease; }

        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .auth-links { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: #4b5563; }
        .auth-links a { color: var(--primary); text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .auth-links a:hover { color: var(--primary-hover); text-decoration: underline; }

        /* Responsive Design */
        @media (max-width: 600px) {
            .auth-card { padding: 1.5rem; }
            body { padding: 0.5rem; }
        }

        /* Accessibilty & Prefers Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        @yield('content')
    </div>

    <script>
        // Hapus indikator error saat user mulai mengetik
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                if(this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    let errorMsg = this.parentNode.querySelector('.error-message');
                    if(errorMsg) errorMsg.remove();
                }
            });
        });

        // Show/Hide Password
       // Show/Hide Password dengan Icon
document.querySelectorAll('.toggle-password').forEach(toggle => {
    toggle.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i'); // Mengambil elemen ikon di dalam span

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash'); // Ganti ke ikon mata dicoret
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye'); // Ganti ke ikon mata normal
        }
    });
});

        // Loading Spinner Form
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('.btn-submit');
                const spinner = btn.querySelector('.spinner');
                const text = btn.querySelector('.btn-text');
                
                if(!btn.disabled) {
                    btn.disabled = true;
                    spinner.style.display = 'block';
                    btn.style.opacity = '0.8';
                    btn.style.cursor = 'not-allowed';
                    this.submit();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>