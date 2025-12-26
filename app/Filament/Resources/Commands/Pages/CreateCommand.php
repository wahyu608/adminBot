<?php

namespace App\Filament\Resources\Commands\Pages;

use App\Filament\Resources\Commands\CommandResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateCommand extends CreateRecord
{
    protected static string $resource = CommandResource::class;
    protected function afterCreate(): void
    {
        Cache::forget("command:{$this->record->command}");
    }
}
