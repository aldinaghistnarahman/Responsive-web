@extends('layouts.app') 

@section('title', 'Data Mahasiswa - HIMITSU') 

@section('content') 
<div class="min-h-screen text-slate-100 py-8 md:py-12" style="background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #0f0728 50%, #050212 100%);"> 
    <div class="container mx-auto px-4 max-w-7xl"> 
        <div class="mb-8 text-center md:text-left"> 
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-cyan-400">Data Mahasiswa Orbit HIMITSU</h1> 
            <p class="text-xs md:text-sm text-slate-400 mt-1">Kelola data manifestasi bintang akademika secara real-time.</p> 
        </div> 

        @if(session('success')) 
            <div class="mb-6 p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 text-sm"> 
                ✨ {{ session('success') }} 
            </div> 
        @endif 

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8"> 
            <!-- FORM INPUT --> 
            <div class="lg:col-span-4"> 
                <div class="border border-slate-800 bg-slate-900/60 p-6 rounded-2xl space-y-4"> 
                    <h2 class="text-base font-bold text-cyan-400 border-b border-slate-800 pb-2">Tambah Anggota Baru</h2> 
                    <form method="post" action="/mahasiswa" class="space-y-4"> 
                        @csrf 
                        <div> 
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap</label> 
                            <input type="text" class="w-full px-3 py-2 text-sm bg-slate-950/50 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500" name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" required> 
                            @error('nama') 
                                <div class="text-rose-400 text-xs mt-1">⚠️ {{ $message }}</div> 
                            @enderror 
                        </div> 
                        <div> 
                            <label class="block text-xs font-semibold text-slate-300 mb-1">NIM</label> 
                            <input type="text" class="w-full px-3 py-2 text-sm bg-slate-950/50 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500" name="nim" value="{{ old('nim') }}" placeholder="Contoh: 22010123" required> 
                            @error('nim') 
                                <div class="text-rose-400 text-xs mt-1">⚠️ {{ $message }}</div> 
                            @enderror 
                        </div> 
                        <div> 
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Email</label> 
                            <input type="email" class="w-full px-3 py-2 text-sm bg-slate-950/50 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required> 
                            @error('email') 
                                <div class="text-rose-400 text-xs mt-1">⚠️ {{ $message }}</div> 
                            @enderror 
                        </div> 
                        <button type="submit" class="w-full py-2.5 px-4 font-bold text-sm text-white rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 transition cursor-pointer">Simpan ke Galaksi</button> 
                    </form> 
                </div> 
            </div> 

            <!-- TABEL / CARD LIST --> 
            <div class="lg:col-span-8"> 
                <div class="border border-slate-800 bg-slate-900/60 p-6 rounded-2xl space-y-4"> 
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-800 pb-3 gap-3">
                        <h2 class="text-base font-bold text-cyan-400">Daftar Manifestasi</h2> 
                        
                        <!-- INPUT LIVE SEARCH KUSTOM -->
                        <div class="w-full md:w-64">
                            <input type="text" id="liveSearch" placeholder="Cari nama, NIM..." class="w-full px-3 py-1.5 text-sm bg-slate-950/80 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-cyan-500 transition">
                        </div>
                    </div>
                    
                    <!-- DESKTOP VIEW --> 
                    <div class="hidden md:block overflow-hidden rounded-xl border border-slate-800/80 bg-slate-950/30"> 
                        <table class="w-full text-left border-collapse text-sm"> 
                            <thead> 
                                <tr class="bg-slate-900/80 border-b border-slate-800 text-xs font-bold uppercase text-slate-400"> 
                                    <th class="py-3 px-4">Nama</th> 
                                    <th class="py-3 px-4 w-36">NIM</th> 
                                    <th class="py-3 px-4">Email</th> 
                                    <th class="py-3 px-4 text-center w-40">Aksi</th> 
                                </tr> 
                            </thead>    
                            <tbody class="divide-y divide-slate-800/60 text-slate-200" id="desktopData"> 
                                @forelse ($mahasiswa as $mhs) 
                                    <tr class="data-row hover:bg-indigo-500/5 transition"> 
                                        <td class="py-4 px-4 font-semibold">{{ $mhs->nama }}</td> 
                                        <td class="py-4 px-4"><span class="font-mono text-xs font-bold text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 px-2 py-0.5 rounded">{{ $mhs->nim }}</span></td> 
                                        <td class="py-4 px-4 text-slate-400 break-all">{{ $mhs->email }}</td> 
                                        <td class="py-4 px-4 text-center"> 
                                            <div class="flex items-center justify-center gap-2"> 
                                                <a href="/mahasiswa/{{ $mhs->id }}/edit" class="text-xs font-bold border border-slate-700 bg-slate-900/50 hover:bg-slate-800 px-2.5 py-1.5 rounded-lg transition">Edit</a> 
                                                <button type="button" onclick="bukaModalHapus('{{ $mhs->id }}')" class="text-xs font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 px-2.5 py-1.5 rounded-lg transition cursor-pointer">Hapus</button> 
                                                
                                                <form id="form-hapus-{{ $mhs->id }}" action="/mahasiswa/{{ $mhs->id }}" method="POST" class="hidden"> 
                                                    @csrf 
                                                    @method('DELETE') 
                                                </form> 
                                            </div> 
                                        </td> 
                                    </tr> 
                                @empty 
                                    <tr class="no-data"><td colspan="4" class="text-center text-slate-500 py-12">🪐 Belum ada entitas mahasiswa yang terorbit.</td></tr> 
                                @endforelse 
                            </tbody> 
                        </table> 
                    </div> 

                    <!-- MOBILE VIEW --> 
                    <div class="block md:hidden space-y-4" id="mobileData"> 
                        @forelse ($mahasiswa as $mhs) 
                            <div class="mobile-card p-4 rounded-xl border border-slate-800 bg-slate-950/40 space-y-3 shadow-inner"> 
                                <div class="flex justify-between border-b border-slate-800/60 pb-2 text-sm"> 
                                    <span class="text-[10px] uppercase font-bold text-slate-500">Nama</span> 
                                    <span class="font-bold text-slate-200">{{ $mhs->nama }}</span> 
                                </div> 
                                <div class="flex justify-between items-center border-b border-slate-800/60 pb-2 text-xs"> 
                                    <span class="text-[10px] uppercase font-bold text-slate-500">NIM</span> 
                                    <span class="font-mono font-bold text-cyan-400 bg-cyan-500/10 border border-cyan-500/25 px-2 py-0.5 rounded">{{ $mhs->nim }}</span> 
                                </div> 
                                <div class="flex justify-between border-b border-slate-800/60 pb-2 text-xs"> 
                                    <span class="text-[10px] uppercase font-bold text-slate-500">Email</span> 
                                    <span class="text-slate-400 break-all max-w-[180px] text-right">{{ $mhs->email }}</span> 
                                </div> 
                                <div class="flex justify-end gap-2 pt-1"> 
                                    <a href="/mahasiswa/{{ $mhs->id }}/edit" class="text-xs font-bold border border-slate-700 bg-slate-900/50 px-3 py-1.5 rounded-lg">Edit</a> 
                                    <button type="button" onclick="bukaModalHapus('{{ $mhs->id }}')" class="text-xs font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 px-3 py-1.5 rounded-lg">Hapus</button> 
                                </div> 
                            </div> 
                        @empty 
                            <div class="no-data text-center text-slate-500 py-8">🪐 Belum ada entitas mahasiswa yang terorbit.</div> 
                        @endforelse 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
</div> 

<!-- COMPONENT MODAL KUSTOM TAILWIND --> 
<div id="modal-konfirmasi" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"> 
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl border border-slate-100 text-center"> 
        <h3 class="text-lg md:text-xl font-bold text-slate-900 mb-6">Yakin ingin menghapus mahasiswa ini?</h3> 
        <div class="flex items-center justify-center gap-4"> 
            <button type="button" onclick="tutupModalHapus()" class="px-6 py-2.5 bg-[#e0e0e0] hover:bg-[#d5d5d5] text-slate-700 text-sm font-semibold rounded-xl min-w-[100px] transition cursor-pointer"> 
                Cancel 
            </button> 
            <button id="btn-konfirmasi-oke" type="button" class="px-6 py-2.5 bg-[#7f1d1d] hover:bg-[#6b1818] text-white text-sm font-semibold rounded-xl min-w-[100px] shadow-lg shadow-rose-900/20 transition cursor-pointer"> 
                OK 
            </button> 
        </div> 
    </div> 
</div>

<!-- SCRIPT PENCARIAN KUSTOM & MODAL -->
<script>
    // --- SCRIPT MODAL HAPUS ---
    let idMahasiswaYangAkanDihapus = null;

    function bukaModalHapus(id) {
        idMahasiswaYangAkanDihapus = id;
        const modal = document.getElementById('modal-konfirmasi');
        modal.classList.remove('hidden');
    }

    function tutupModalHapus() {
        idMahasiswaYangAkanDihapus = null;
        const modal = document.getElementById('modal-konfirmasi');
        modal.classList.add('hidden');
    }

    document.getElementById('btn-konfirmasi-oke').addEventListener('click', function() {
        if (idMahasiswaYangAkanDihapus) {
            const formHapus = document.getElementById('form-hapus-' + idMahasiswaYangAkanDihapus);
            if (formHapus) {
                formHapus.submit();
            }
        }
    });

    // --- SCRIPT LIVE SEARCH KUSTOM ---
    $(document).ready(function(){
        $("#liveSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            
            // Filter pada tampilan Desktop (Tabel)
            $("#desktopData tr.data-row").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });

            // Filter pada tampilan Mobile (Card)
            $("#mobileData div.mobile-card").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection