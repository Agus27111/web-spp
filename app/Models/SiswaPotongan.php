<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiswaPotongan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['siswa_id', 'potongan_id'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function potongan() { return $this->belongsTo(Potongan::class); }
}
