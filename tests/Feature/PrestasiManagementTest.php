<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\Prestasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrestasiManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_prestasi_can_be_created_for_multiple_mahasiswa(): void
    {
        $this->actingAs(User::factory()->create());
        $andi = Mahasiswa::create(['nama' => 'Andi', 'nim' => '1001', 'email' => 'andi@example.test']);
        $budi = Mahasiswa::create(['nama' => 'Budi', 'nim' => '1002', 'email' => 'budi@example.test']);

        $response = $this->post('/prestasi', [
            'nama_prestasi' => 'Juara 1 Hackathon 2026',
            'tingkat' => 'Nasional',
            'tahun' => 2026,
            'deskripsi' => 'Pemenang kompetisi.',
            'mahasiswa_ids' => [$andi->id, $budi->id],
        ]);

        $response->assertRedirect('/prestasi');
        $prestasi = Prestasi::where('nama_prestasi', 'Juara 1 Hackathon 2026')->firstOrFail();
        $this->assertEqualsCanonicalizing([$andi->id, $budi->id], $prestasi->mahasiswas->modelKeys());
        $this->assertDatabaseHas('mahasiswa_prestasi', ['mahasiswa_id' => $andi->id, 'prestasi_id' => $prestasi->id]);
    }

    public function test_prestasi_can_be_created_for_one_mahasiswa(): void
    {
        $this->actingAs(User::factory()->create());
        $mahasiswa = Mahasiswa::create(['nama' => 'Citra', 'nim' => '1003', 'email' => 'citra@example.test']);

        $this->post('/prestasi', [
            'nama_prestasi' => 'Juara Debat Kampus',
            'tingkat' => 'Kampus',
            'tahun' => 2026,
            'deskripsi' => 'Juara pertama lomba debat.',
            'mahasiswa_ids' => [$mahasiswa->id],
        ])->assertRedirect('/prestasi');

        $prestasi = Prestasi::where('nama_prestasi', 'Juara Debat Kampus')->firstOrFail();
        $this->assertEquals([$mahasiswa->id], $prestasi->mahasiswas->modelKeys());
    }

    public function test_mahasiswa_search_by_prestasi_keeps_filter_across_pages(): void
    {
        $this->actingAs(User::factory()->create());
        $prestasi = Prestasi::create([
            'nama_prestasi' => 'Hackathon Nasional',
            'tingkat' => 'Nasional',
            'tahun' => 2026,
            'deskripsi' => 'Kompetisi.',
        ]);

        for ($number = 1; $number <= 11; $number++) {
            $mahasiswa = Mahasiswa::create([
                'nama' => 'Mahasiswa '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'nim' => (string) (2000 + $number),
                'email' => 'mahasiswa'.$number.'@example.test',
            ]);
            $prestasi->mahasiswas()->attach($mahasiswa);
        }
        Mahasiswa::create(['nama' => 'Mahasiswa Lain', 'nim' => '9000', 'email' => 'lain@example.test']);

        $firstPage = $this->get('/mahasiswa?prestasi=Hackathon')
            ->assertOk()
            ->assertSee('Mahasiswa 01')
            ->assertDontSee('Mahasiswa Lain');
        $firstPage->assertViewHas('page', fn (array $page) => count($page['mahasiswa']) === 5 && $page['pagination']['lastPage'] === 3);

        $this->get('/mahasiswa?prestasi=Hackathon&page=2')
            ->assertOk()
            ->assertSee('Mahasiswa 06')
            ->assertDontSee('Mahasiswa Lain');

        $this->get('/mahasiswa?prestasi=Hackathon&page=3')
            ->assertOk()
            ->assertSee('Mahasiswa 11')
            ->assertDontSee('Mahasiswa Lain');
    }

    public function test_exact_prestasi_search_does_not_include_students_with_only_similar_titles(): void
    {
        $this->actingAs(User::factory()->create());
        $exact = Prestasi::create([
            'nama_prestasi' => 'Juara 1 Hackathon 2026',
            'tingkat' => 'Nasional',
            'tahun' => 2026,
            'deskripsi' => 'Pencapaian pilihan.',
        ]);
        $similar = Prestasi::create([
            'nama_prestasi' => 'Juara 1 Hackathon 2026 Regional',
            'tingkat' => 'Provinsi',
            'tahun' => 2026,
            'deskripsi' => 'Pencapaian dengan judul mirip.',
        ]);
        $andi = Mahasiswa::create(['nama' => 'Andi', 'nim' => '4101', 'email' => 'andi-exact@example.test']);
        $budi = Mahasiswa::create(['nama' => 'Budi', 'nim' => '4102', 'email' => 'budi-similar@example.test']);
        $exact->mahasiswas()->attach($andi);
        $similar->mahasiswas()->attach($budi);

        $this->get('/mahasiswa?prestasi='.urlencode('Juara 1 Hackathon 2026'))
            ->assertOk()
            ->assertSee('Andi')
            ->assertDontSee('Budi')
            ->assertViewHas('page', fn (array $page) => $page['pagination']['total'] === 1);
    }

    public function test_search_without_matches_returns_an_empty_result_set(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/mahasiswa?prestasi=Prestasi%20yang%20tidak%20ada')
            ->assertOk()
            ->assertViewHas('page', fn (array $page) => $page['filter'] === 'Prestasi yang tidak ada' && $page['mahasiswa']->isEmpty());
    }

    public function test_prestasi_input_is_validated(): void
    {
        $this->actingAs(User::factory()->create())
            ->from('/prestasi/create')
            ->post('/prestasi', [
                'nama_prestasi' => '',
                'tingkat' => 'Planet',
                'tahun' => 1800,
                'deskripsi' => '',
                'mahasiswa_ids' => [999],
            ])
            ->assertRedirect('/prestasi/create')
            ->assertSessionHasErrors(['nama_prestasi', 'tingkat', 'tahun', 'deskripsi', 'mahasiswa_ids.0']);
    }

    public function test_prestasi_requires_at_least_one_mahasiswa(): void
    {
        $this->actingAs(User::factory()->create())
            ->from('/prestasi/create')
            ->post('/prestasi', [
                'nama_prestasi' => 'Juara Lomba',
                'tingkat' => 'Kampus',
                'tahun' => 2026,
                'deskripsi' => 'Pencapaian mahasiswa.',
            ])
            ->assertRedirect('/prestasi/create')
            ->assertSessionHasErrors('mahasiswa_ids');
    }

    public function test_prestasi_can_be_updated_and_deleted_with_its_links(): void
    {
        $this->actingAs(User::factory()->create());
        $andi = Mahasiswa::create(['nama' => 'Andi', 'nim' => '3001', 'email' => 'andi-update@example.test']);
        $budi = Mahasiswa::create(['nama' => 'Budi', 'nim' => '3002', 'email' => 'budi-update@example.test']);
        $prestasi = Prestasi::create([
            'nama_prestasi' => 'Lomba Lama',
            'tingkat' => 'Kampus',
            'tahun' => 2025,
            'deskripsi' => 'Deskripsi lama.',
        ]);
        $prestasi->mahasiswas()->attach($andi);

        $this->put('/prestasi/'.$prestasi->id, [
            'nama_prestasi' => 'Lomba Baru',
            'tingkat' => 'Provinsi',
            'tahun' => 2026,
            'deskripsi' => 'Deskripsi baru.',
            'mahasiswa_ids' => [$budi->id],
        ])->assertRedirect('/prestasi');

        $this->assertDatabaseHas('prestasis', ['id' => $prestasi->id, 'nama_prestasi' => 'Lomba Baru']);
        $this->assertEquals([$budi->id], $prestasi->fresh()->mahasiswas->modelKeys());

        $this->delete('/prestasi/'.$prestasi->id)->assertRedirect('/prestasi');
        $this->assertDatabaseMissing('prestasis', ['id' => $prestasi->id]);
        $this->assertDatabaseMissing('mahasiswa_prestasi', ['prestasi_id' => $prestasi->id]);
    }

    public function test_successful_login_opens_the_home_page(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect('/dashboard');

        $this->get('/dashboard')->assertOk()->assertSee('dashboardPage');
    }

    public function test_registration_returns_to_login_without_authenticating_the_new_account(): void
    {
        $this->post('/register', [
            'nama' => 'Akun Baru',
            'email' => 'akun-baru@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/login')->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['email' => 'akun-baru@example.test']);
        $this->assertGuest();

        $this->post('/login', [
            'email' => 'akun-baru@example.test',
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }

    public function test_prestasi_list_displays_five_records_per_page(): void
    {
        $this->actingAs(User::factory()->create());

        for ($number = 1; $number <= 6; $number++) {
            Prestasi::create([
                'nama_prestasi' => 'Prestasi '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'tingkat' => 'Kampus',
                'tahun' => 2026,
                'deskripsi' => 'Prestasi uji.',
            ]);
        }

        $this->get('/prestasi')
            ->assertOk()
            ->assertViewHas('page', fn (array $page) => count($page['prestasis']) === 5 && $page['pagination']['lastPage'] === 2);

        $this->get('/prestasi?page=2')
            ->assertOk()
            ->assertSee('Prestasi 06');
    }
}
