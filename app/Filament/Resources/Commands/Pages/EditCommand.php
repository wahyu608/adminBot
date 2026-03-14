<?php

namespace App\Filament\Resources\Commands\Pages;

use App\Filament\Resources\Commands\CommandResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;
use App\Helpers\CloudinaryHelper;

class EditCommand extends EditRecord
{
    protected static string $resource = CommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
            ->after(function () {
                $record = $this->record;

                if ($record && $record->photo) {
                    CloudinaryHelper::deleteByUrl($record->photo);
                }
            }),
        ];
    }

    protected function afterSave(): void
    {
        Cache::forget("command:{$this->record->command}");
    }
}

