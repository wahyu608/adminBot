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
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
