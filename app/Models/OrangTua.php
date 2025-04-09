<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrangTua extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama', 'nomor_hp'];

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
}
