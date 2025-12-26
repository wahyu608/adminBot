<?php

namespace App\Filament\Resources\Stafs\Pages;

use App\Filament\Resources\Stafs\StafResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStafs extends ListRecords
{
    protected static string $resource = StafResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
