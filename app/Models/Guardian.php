<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $table = 'guardians';

    protected $fillable = ['foundation_id', 'name', 'phone_number'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }
}
