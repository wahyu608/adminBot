<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staf extends Model
{
    protected $table = 'stafs';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone_number',
        'position',
        'student_academic_services',
        'photo',
    ];
    protected static function booted()
    {

    }
}
