<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['orangtua_id', 'nama', 'nisn', 'tanggal_lahir'];

    public function orangtua() { return $this->belongsTo(OrangTua::class); }
    public function siswaTahun() { return $this->hasMany(SiswaTahun::class); }
    public function siswaPotongans() { return $this->hasMany(SiswaPotongan::class); }
}
