<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Foundation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'address', 'image'];

    // Relasi Foundation -> User (Admin/Manager)
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
