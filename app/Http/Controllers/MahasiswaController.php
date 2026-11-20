<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Prestasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'prestasi' => ['nullable', 'string', 'max:255'],
        ]);
        $prestasi = $filters['prestasi'] ?? '';
        $hasExactPrestasi = $prestasi !== ''
            && Prestasi::where('nama_prestasi', $prestasi)->exists();

        $mahasiswa = Mahasiswa::query()
            ->with('prestasis')
            ->when($prestasi !== '', function ($query) use ($prestasi, $hasExactPrestasi) {
                $query->whereHas('prestasis', function ($prestasiQuery) use ($prestasi, $hasExactPrestasi) {
                    if ($hasExactPrestasi) {
                        $prestasiQuery->where('nama_prestasi', $prestasi);
                    } else {
                        $prestasiQuery->where('nama_prestasi', 'like', '%'.$prestasi.'%');
                    }
                });
            })
            ->orderBy('nama')
            ->paginate(5)
            ->withQueryString();

        return view('app', [
            'page' => [
                'name' => 'mahasiswa.index',
                'success' => session('success'),
                'filter' => $prestasi,
                'prestasiOptions' => Prestasi::query()->orderBy('nama_prestasi')->pluck('nama_prestasi')->unique()->values(),
                'mahasiswa' => $mahasiswa->getCollection()->map(fn (Mahasiswa $mhs) => [
                    'id' => $mhs->id,
                    'nama' => $mhs->nama,
                    'nim' => $mhs->nim,
                    'email' => $mhs->email,
                    'prestasis' => $mhs->prestasis->pluck('nama_prestasi'),
                ])->values(),
                'pagination' => $this->pagination($mahasiswa),
                'old' => old(),
                'errors' => session('errors')?->getBag('default')->messages() ?? [],
                'csrfToken' => csrf_token(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nim' => ['required', 'numeric', 'unique:mahasiswas,nim'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:mahasiswas,email'],
        ]);

        Mahasiswa::create($data);

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        return view('app', [
            'page' => [
                'name' => 'mahasiswa.edit',
                'mahasiswa' => [
                    'id' => $mahasiswa->id,
                    'nama' => $mahasiswa->nama,
                    'nim' => $mahasiswa->nim,
                    'email' => $mahasiswa->email,
                ],
                'old' => old(),
                'errors' => session('errors')?->getBag('default')->messages() ?? [],
                'csrfToken' => csrf_token(),
            ],
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $request->validate([
            'nim' => ['required', 'numeric', Rule::unique('mahasiswas', 'nim')->ignore($mahasiswa->id)],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('mahasiswas', 'email')->ignore($mahasiswa->id)],
        ]);

        $mahasiswa->update($data);

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    private function pagination($paginator): array
    {
        return [
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
            'previousPageUrl' => $paginator->previousPageUrl(),
            'nextPageUrl' => $paginator->nextPageUrl(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }
}
