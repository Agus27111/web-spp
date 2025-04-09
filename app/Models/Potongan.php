<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Potongan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['siswa_tahun_id', 'jenis_tarif_id', 'nama_potongan', 'jumlah'];

    public function siswaTahun() { return $this->belongsTo(SiswaTahun::class); }
    public function jenisTarif() { return $this->belongsTo(JenisTarif::class); }
}
