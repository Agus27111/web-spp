<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['foundation_id', 'name', 'email', 'password', 'phone_number', 'role'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi User -> Foundation (terkait Foundation)
    public function foundation()
    {
        return $this->belongsTo(Foundation::class); // Seorang user memiliki 1 foundation_id
    }

    // Untuk memastikan role adalah foundation
    public function isFoundation()
    {
        return $this->role === 'foundation';
    }

    // Untuk memastikan user adalah operator
    public function isOperator()
    {
        return $this->role === 'operator';
    }

    // Untuk memastikan user adalah parent
    public function isParent()
    {
        return $this->role === 'parent';
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
