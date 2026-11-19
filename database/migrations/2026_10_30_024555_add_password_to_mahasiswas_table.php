<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Ubah 'mahasiswa' menjadi 'mahasiswas'
    Schema::table('mahasiswas', function (Blueprint $table) {
        $table->string('password')->nullable();
    });
}

public function down(): void
{
    // Ubah 'mahasiswa' menjadi 'mahasiswas'
    Schema::table('mahasiswas', function (Blueprint $table) {
        $table->dropColumn('password');
    });
}

};
