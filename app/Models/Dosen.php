<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosens';

    protected $fillable = [
        'name',
        'nidn',
        'email',
        'phone_number',
        'position',
        'study_program',
        'description',
        'photo',
    ];

    protected function foto(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value 
                ? \Storage::disk('cloudinary')->url($value)
                : null,
        );
    }
}
