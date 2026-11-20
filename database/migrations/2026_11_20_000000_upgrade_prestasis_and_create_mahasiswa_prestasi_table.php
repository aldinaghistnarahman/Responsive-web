<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestasis', function (Blueprint $table) {
            if (! Schema::hasColumn('prestasis', 'nama_prestasi')) {
                $table->string('nama_prestasi')->nullable();
            }
            if (! Schema::hasColumn('prestasis', 'tingkat')) {
                $table->string('tingkat')->nullable();
            }
            if (! Schema::hasColumn('prestasis', 'tahun')) {
                $table->unsignedSmallInteger('tahun')->nullable();
            }
            if (! Schema::hasColumn('prestasis', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
        });

        if (Schema::hasColumn('prestasis', 'mahasiswa_id')) {
            Schema::table('prestasis', function (Blueprint $table) {
                $table->unsignedBigInteger('mahasiswa_id')->nullable()->change();
            });
        }

        if (Schema::hasColumn('prestasis', 'judul')) {
            Schema::table('prestasis', function (Blueprint $table) {
                $table->string('judul')->nullable()->change();
            });
        }

        if (Schema::hasColumn('prestasis', 'judul')) {
            DB::table('prestasis')
                ->whereNull('nama_prestasi')
                ->update(['nama_prestasi' => DB::raw('judul')]);
        }

        DB::table('prestasis')
            ->whereNull('tingkat')
            ->update(['tingkat' => 'Kampus']);
        DB::table('prestasis')
            ->whereNull('tahun')
            ->update(['tahun' => (int) date('Y')]);
        DB::table('prestasis')
            ->whereNull('deskripsi')
            ->update(['deskripsi' => '']);

        if (! Schema::hasTable('mahasiswa_prestasi')) {
            Schema::create('mahasiswa_prestasi', function (Blueprint $table) {
                $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
                $table->foreignId('prestasi_id')->constrained('prestasis')->cascadeOnDelete();
                $table->primary(['mahasiswa_id', 'prestasi_id']);
            });
        }

        if (Schema::hasColumn('prestasis', 'mahasiswa_id')) {
            DB::table('prestasis')
                ->whereNotNull('mahasiswa_id')
                ->orderBy('id')
                ->get(['id', 'mahasiswa_id'])
                ->each(function ($prestasi) {
                    DB::table('mahasiswa_prestasi')->insertOrIgnore([
                        'mahasiswa_id' => $prestasi->mahasiswa_id,
                        'prestasi_id' => $prestasi->id,
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_prestasi');
        Schema::table('prestasis', function (Blueprint $table) {
            $table->dropColumn(['nama_prestasi', 'tingkat', 'tahun', 'deskripsi']);
        });
    }
};
