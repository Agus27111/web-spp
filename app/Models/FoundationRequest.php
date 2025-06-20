<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class FoundationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'status',
    ];

    public static function rules(): array
{
    return [
        'email' => [
            'required',
            'email',
            Rule::unique('foundation_requests')->where(function ($query) {
                return $query->whereIn('status', ['pending', 'approved']);
            })
        ]
    ];
}
}
