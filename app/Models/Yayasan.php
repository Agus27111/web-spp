<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Yayasan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama', 'alamat'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function units()
    {
        return $this->hasMany(Unit::class);
    }
    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
