<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Foundation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'address', 'image', 'phone_number'];

    public function scopeForUser($query)
    {
        if (Auth::user()->role === 'superadmin') {
            return $query; // superadmin boleh lihat semua foundation
        }

        return $query->where('foundations.id', Auth::user()->foundation_id);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Foundation -> Users (Operator, Parent)
    public function users()
    {
        return $this->hasMany(User::class); // Sebuah foundation memiliki banyak user dengan role operator/parent
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
