<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CloudinaryHelper;

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
        static::deleting(function ($model) {
        \Log::info('Deleting photo: ' . $model->photo);
        CloudinaryHelper::deleteByUrl($model->photo);
        });

        static::updating(function ($model) {
            if ($model->isDirty('photo')) {
                CloudinaryHelper::deleteByUrl($model->getOriginal('photo'));
            }
        });
    }
}
