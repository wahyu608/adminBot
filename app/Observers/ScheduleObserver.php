<?php

namespace App\Observers;

use App\Models\Schedule;
use App\Helpers\SlugCacheHelper as SlugCache;
use Illuminate\Support\Facades\Cache;

class ScheduleObserver
{
    public function saved(Schedule $schedule): void
    {
        SlugCache::clear();
        Cache::forget("command:{$schedule->slug}");
        Cache::forget("command:schedule");
        $oldSlug = $schedule->getOriginal('slug');

        if ($oldSlug && $oldSlug !== $schedule->slug) {
            Cache::forget("command:$oldSlug");
        }
    }

    public function deleted(Schedule $schedule): void
    {
        SlugCache::clear();
        Cache::forget("command:{$schedule->slug}");
        Cache::forget("command:schedule");
    }
}