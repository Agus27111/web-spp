<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone_number', 'address', 'status', 'email_verified_at'
    ];
}
