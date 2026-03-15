<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosens';

    protected $fillable = [
        'name',
        'slug',
        'nidn',
        'email',
        'phone_number',
        'position',
        'study_program',
        'bio',
        'photo',
    ];

    protected function foto(): Attribute
    {
    }
}
