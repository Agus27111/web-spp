<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['yayasan_id', 'nama'];

    public function yayasan() { return $this->belongsTo(Yayasan::class); }
    public function kelas() { return $this->hasMany(Kelas::class); }
}
