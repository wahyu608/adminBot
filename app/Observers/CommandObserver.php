<?php

namespace App\Observers;

use App\Models\Command;
use App\Helpers\SlugCacheHelper as SlugCache;
use Illuminate\Support\Facades\Cache;

class CommandObserver
{
    public function saved(Command $command): void
    {
        SlugCache::clear();
        Cache::forget("command:{$command->command}");
        $oldCommand = $command->getOriginal('command');

        if ($oldCommand && $oldCommand !== $command->command) {
            Cache::forget("command:$oldCommand");
        }
    }

    public function deleted(Command $command): void
    {
        SlugCache::clear();
        Cache::forget("command:{$command->command}");
    }
}