<?php

namespace App\Filament\Resources\Dosens\Pages;

use App\Filament\Resources\Dosens\DosenResource;
use Filament\Actions\{DeleteAction,ViewAction};
use Filament\Resources\Pages\EditRecord;
use App\Helpers\CloudinaryHelper;

class EditDosen extends EditRecord
{
    protected static string $resource = DosenResource::class;

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
