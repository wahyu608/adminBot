<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class SlugCacheHelper
{
    public static function clear(): void
    {
        Cache::forget('slug_index');
    }
}