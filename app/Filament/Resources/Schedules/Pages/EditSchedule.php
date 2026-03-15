<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Helpers\CloudinaryHelper;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

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
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $oldPhoto = $this->record->photo ?? null;
        $newPhoto = $data['photo'] ?? null;

    
        if ($oldPhoto && $newPhoto && $oldPhoto !== $newPhoto) {
            CloudinaryHelper::deleteByUrl($oldPhoto);
        }

        return $data;
    }
}
