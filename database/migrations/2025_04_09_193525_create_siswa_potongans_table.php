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
        Schema::create('siswa_potongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_tahun_id')->constrained()->onDelete('cascade');
            $table->foreignId('jenis_tarif_id')->constrained()->onDelete('cascade');
            $table->foreignId('potongan_id')->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_potongans');
    }
};
