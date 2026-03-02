<?php

namespace App\Filament\Resources\Stafs\Pages;

use App\Filament\Resources\Stafs\StafResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStaf extends EditRecord
{
    protected static string $resource = StafResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
