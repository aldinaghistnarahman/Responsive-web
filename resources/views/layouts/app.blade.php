<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Data Mahasiswa')</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.3/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
    
    <!-- PENTING: Skrip CDN resmi untuk meload semua class Tailwind langsung dari browser -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <!-- PENTING: Urutan Script Diperbaiki (jQuery WAJIB di atas DataTables & Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/3.1.3/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
</head>

<body class="bg-[#050212] text-white min-h-screen">

    <!-- Navbar & Tombol Logout -->
    <nav class="flex justify-between items-center px-6 py-4 bg-gray-900 border-b border-gray-800 shadow-md">
        <h1 class="text-xl font-bold text-white tracking-wide">Panel Mahasiswa</h1>
        
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-semibold transition-colors duration-200">
                Logout
            </button>
        </form>
    </nav>

    <!-- Area pemanggilan konten utama -->
    <main class="p-6">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>