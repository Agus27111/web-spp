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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_tahun_id')->constrained()->onDelete('cascade');
            $table->foreignId('tarif_id')->constrained()->onDelete('cascade');
            $table->string('bulan');
            $table->date('tanggal_bayar');
            $table->integer('total_tagihan');
            $table->integer('total_potongan');
            $table->integer('jumlah_dibayar');
            $table->string('metode_pembayaran');
            $table->string('file_pdf')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
