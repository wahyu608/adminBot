<?php

namespace App\Filament\Resources\Commands\Pages;

use App\Filament\Resources\Commands\CommandResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditCommand extends EditRecord
{
    protected static string $resource = CommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->after(fn () =>
                    Cache::forget("command:{$this->record->command}")
            ),
        ];
    }

    protected function afterSave(): void
    {
        Cache::forget("command:{$this->record->command}");
    }
}

