<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['yayasan_id', 'nama', 'jumlah', 'tanggal', 'keterangan'];

    public function yayasan()
    {
        return $this->belongsTo(Yayasan::class);
    }
}
