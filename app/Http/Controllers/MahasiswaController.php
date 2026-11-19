<?php

namespace App\Http\Controllers;

use App\Models\mahasiswa;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    // 1. Tampilkan Halaman Utama
    public function index()
    {
        $mahasiswa = mahasiswa::with('prestasis')
            ->orderBy('nama')
            ->get(); 
        return view('mahasiswa.index', compact('mahasiswa'));
    }

    // 2. Simpan Data Tambah Baru (Sudah Diperbaiki)
    public function store(Request $request)
    {

        $data = $request->validate([
            // WAJIB MENAMBAHKAN VALIDASI NIM DI SINI
            'nim' => 'required|numeric|unique:mahasiswas,nim',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:mahasiswas,email',
        ], [
            'nim.required' => 'NIM wajib diisi!',
            'nim.numeric' => 'NIM harus berupa angka!',
            'nim.unique' => 'NIM ini sudah terdaftar!',
            'email.email' => 'Format email tidak valid (harus mengandung @ dan domain)!',
            'email.unique' => 'Email ini sudah terdaftar, silakan gunakan email lain!',
        ]);
        mahasiswa::create($data);

        return redirect('/mahasiswa')->with('success', 'Data mahasiswa berhasil ditambahkan!');
    }

    // 3. Buka Halaman Edit
    public function edit($id)
    {
        $mhs = mahasiswa::findOrFail($id);
        return view('mahasiswa.edit', compact('mhs'));
    }

    // 4. Simpan Perubahan Edit (Sudah Diperbaiki)
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            // WAJIB MENAMBAHKAN VALIDASI NIM DI SINI JUGA
            'nim' => 'required|numeric|unique:mahasiswas,nim,' . $id,
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:mahasiswas,email,' . $id,
        ], [
            'nim.numeric' => 'NIM harus berupa angka!',
            'nim.unique' => 'NIM ini sudah digunakan!',
            'email.email' => 'Format email tidak valid (harus mengandung @ dan domain)!',
            'email.unique' => 'Email ini sudah terdaftar, silakan gunakan email lain!',
        ]);

        $mhs = mahasiswa::findOrFail($id);
        $mhs->update($data);

        return redirect('/mahasiswa')->with('success', 'Data mahasiswa berhasil diperbarui!');
    }

    // 5. Hapus Data dari Database
    public function destroy($id)
    {
        $mhs = mahasiswa::findOrFail($id);
        $mhs->delete();

        return redirect('/mahasiswa')->with('success', 'Data mahasiswa berhasil dihapus!');
    }
}
