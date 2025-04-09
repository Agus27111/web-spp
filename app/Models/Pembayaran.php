<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['siswa_tahun_id', 'tarif_id', 'bulan', 'tanggal_bayar', 'total_tagihan', 'total_potongan', 'jumlah_dibayar', 'metode_pembayaran', 'file_pdf'];

    public function siswaTahun()
    {
        return $this->belongsTo(SiswaTahun::class);
    }
    public function tarif()
    {
        return $this->belongsTo(Tarif::class);
    }
}
