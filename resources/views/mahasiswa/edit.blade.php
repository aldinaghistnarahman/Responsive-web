<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa - Cosmic Edition</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; color: #f1f5f9; min-height: 100vh; 
            display: flex; justify-content: center; align-items: center; padding: 20px; overflow: hidden;
            background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #0f0728 50%, #050212 100%);
        }
        body::before {
            content: ""; position: absolute; width: 200%; height: 200%; opacity: 0.25;
            background-image: radial-gradient(circle at 50% 50%, #ffffff 1px, transparent 1px);
            background-size: 250px 250px; animation: rotateGalaxy 120s linear infinite; pointer-events: none;
        }
        @keyframes rotateGalaxy { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .card-edit { 
            background: rgba(15, 12, 38, 0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            padding: 32px; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.08); width: 100%; max-width: 440px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        }
        h2 { font-size: 22px; font-weight: 700; color: #ffffff; text-align: center; margin-bottom: 4px; text-shadow: 0 0 10px rgba(168, 85, 247, 0.4); }
        .subtitle { color: #94a3b8; font-size: 13px; text-align: center; margin-bottom: 24px; }
        form { display: flex; flex-direction: column; gap: 18px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 13px; font-weight: 500; color: #94a3b8; }
        input { width: 100%; padding: 12px 16px; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; font-size: 14px; outline: none; background-color: rgba(255, 255, 255, 0.05); color: #ffffff; transition: all 0.3s ease; }
        input:focus { border-color: #a855f7; box-shadow: 0 0 12px rgba(168, 85, 247, 0.4); }
        .btn-container { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 10px; }
        button.btn-update { padding: 12px; background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 14px rgba(168, 85, 247, 0.3); }
        button.btn-update:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(236, 72, 153, 0.5); }
        .btn-back { display: flex; align-items: center; justify-content: center; padding: 12px; background-color: rgba(255, 255, 255, 0.05); color: #cbd5e1; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.1); }
        .btn-back:hover { background-color: rgba(255, 255, 255, 0.15); color: #ffffff; }
        .alert-danger { color: #f87171; font-size: 12px; margin-top: 2px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="card-edit">
        <h2>Modifikasi Orbit</h2>
        <div class="subtitle">Sesuaikan lintasan data bintang mahasiswa</div>
        <form method="post" action="/mahasiswa/{{ $mhs->id }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $mhs->nama) }}" required>
                @error('nama') <div class="alert-danger">⚠️ {{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="nim">NIM (Nomor Induk Mahasiswa)</label>
                <input type="text" id="nim" name="nim" value="{{ old('nim', $mhs->nim) }}" required>
                @error('nim') <div class="alert-danger">⚠️ {{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $mhs->email) }}" required>
                @error('email') <div class="alert-danger">⚠️ {{ $message }}</div> @enderror
            </div>
            <div class="btn-container">
                <a href="/mahasiswa" class="btn-back">Batal</a>
                <button type="submit" class="btn-update">Perbarui Lintasan</button>
            </div>
        </form>
    </div>
</body>
</html>
