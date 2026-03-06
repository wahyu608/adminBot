<?php

namespace App\Observers;

use App\Models\Dosen;
use App\Helpers\SlugCacheHelper as SlugCache;
use Illuminate\Support\Facades\Cache;

class DosenObserver
{
    public function saved(Dosen $dosen): void
    {
        SlugCache::clear();
        Cache::forget("command:{$dosen->slug}");
        Cache::forget("command:dosen");
        $oldSlug = $dosen->getOriginal('slug');

        if ($oldSlug && $oldSlug !== $dosen->slug) {
            Cache::forget("command:$oldSlug");
        }
    }

    public function deleted(Dosen $dosen): void
    {
        SlugCache::clear();
        Cache::forget("command:{$dosen->slug}");
        Cache::forget("command:dosen");
    }
}