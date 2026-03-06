<?php

namespace App\Observers;

use App\Models\Staf;
use App\Helpers\SlugCacheHelper as SlugCache;
use Illuminate\Support\Facades\Cache;

class StafObserver
{
    public function saved(Staf $staf): void
    {
        SlugCache::clear();
        Cache::forget("command:{$staf->slug}");
        Cache::forget("command:staf");
        $oldSlug = $staf->getOriginal('slug');

        if ($oldSlug && $oldSlug !== $staf->slug) {
            Cache::forget("command:$oldSlug");
        }
    }

    public function deleted(Staf $staf): void
    {
        SlugCache::clear();
        Cache::forget("command:{$staf->slug}");
        Cache::forget("command:staf");
        
    }
}
