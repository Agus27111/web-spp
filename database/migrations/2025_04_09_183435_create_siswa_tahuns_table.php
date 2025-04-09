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
        Schema::create('siswa_tahuns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained()->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['aktif', 'lulus', 'keluar'])->default('aktif');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_tahuns');
    }
};
