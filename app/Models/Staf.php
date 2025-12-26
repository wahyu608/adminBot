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
        'description',
        'photo',
    ];
}
