<?php

namespace App\Filament\Resources\Stafs\Pages;

use App\Filament\Resources\Stafs\StafResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStaf extends ViewRecord
{
    protected static string $resource = StafResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
