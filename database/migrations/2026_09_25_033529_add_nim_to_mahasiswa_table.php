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
    Schema::table('mahasiswas', function (Blueprint $table) {
        // Dibuat nullable dulu agar data lama tidak error
        $table->string('nim')->nullable()->after('id'); 
    });
}

public function down(): void
{
    Schema::table('mahasiswas', function (Blueprint $table) {
        $table->dropColumn('nim');
    });
}
};
