<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Prestasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrestasiController extends Controller
{
    private const TINGKAT = ['Kampus', 'Kota', 'Provinsi', 'Nasional', 'Internasional'];

    public function index()
    {
        $prestasis = Prestasi::with('mahasiswas')
            ->orderByDesc('tahun')
            ->orderBy('nama_prestasi')
            ->paginate(5)
            ->withQueryString();

        return view('app', [
            'page' => [
                'name' => 'prestasi.index',
                'success' => session('success'),
                'prestasis' => $prestasis->getCollection()->map(fn (Prestasi $prestasi) => $this->serializePrestasi($prestasi))->values(),
                'pagination' => $this->pagination($prestasis),
                'csrfToken' => csrf_token(),
            ],
        ]);
    }

    public function create()
    {
        return $this->formPage('prestasi.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $mahasiswaIds = $data['mahasiswa_ids'] ?? [];
        unset($data['mahasiswa_ids']);

        $prestasi = Prestasi::create($data);
        $prestasi->mahasiswas()->sync($mahasiswaIds);

        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(Prestasi $prestasi)
    {
        return $this->formPage('prestasi.edit', $prestasi->load('mahasiswas'));
    }

    public function update(Request $request, Prestasi $prestasi)
    {
        $data = $this->validatedData($request);
        $mahasiswaIds = $data['mahasiswa_ids'] ?? [];
        unset($data['mahasiswa_ids']);

        $prestasi->update($data);
        $prestasi->mahasiswas()->sync($mahasiswaIds);

        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(Prestasi $prestasi)
    {
        $prestasi->delete();

        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil dihapus.');
    }

    private function formPage(string $name, ?Prestasi $prestasi = null)
    {
        return view('app', [
            'page' => [
                'name' => $name,
                'prestasi' => $prestasi ? $this->serializePrestasi($prestasi) : null,
                'mahasiswaOptions' => Mahasiswa::query()->orderBy('nama')->get(['id', 'nama', 'nim']),
                'tingkatOptions' => self::TINGKAT,
                'old' => old(),
                'errors' => session('errors')?->getBag('default')->messages() ?? [],
                'csrfToken' => csrf_token(),
            ],
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama_prestasi' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', Rule::in(self::TINGKAT)],
            'tahun' => ['required', 'integer', 'min:1900', 'max:'.((int) date('Y') + 1)],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'mahasiswa_ids' => ['required', 'array', 'min:1'],
            'mahasiswa_ids.*' => ['integer', 'distinct', 'exists:mahasiswas,id'],
        ]);
    }

    private function serializePrestasi(Prestasi $prestasi): array
    {
        return [
            'id' => $prestasi->id,
            'nama_prestasi' => $prestasi->nama_prestasi,
            'tingkat' => $prestasi->tingkat,
            'tahun' => $prestasi->tahun,
            'deskripsi' => $prestasi->deskripsi,
            'mahasiswa' => $prestasi->mahasiswas->map(fn (Mahasiswa $mhs) => [
                'id' => $mhs->id,
                'nama' => $mhs->nama,
                'nim' => $mhs->nim,
            ])->values(),
        ];
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
