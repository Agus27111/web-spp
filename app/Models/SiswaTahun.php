<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiswaTahun extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['siswa_id', 'tahun_ajaran_id', 'kelas_id', 'status'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function potongans() { return $this->hasMany(Potongan::class); }
    public function pembayarans() { return $this->hasMany(Pembayaran::class); }
}
