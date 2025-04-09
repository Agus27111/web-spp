<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarif extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['jenis_tarif_id', 'tahun_ajaran_id', 'kelas_id', 'nominal'];

    public function jenisTarif() { return $this->belongsTo(JenisTarif::class); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function pembayarans() { return $this->hasMany(Pembayaran::class); }
}
