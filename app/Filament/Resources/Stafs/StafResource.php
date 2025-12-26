<?php

namespace App\Filament\Resources\Stafs;

use App\Filament\Resources\Stafs\Pages\CreateStaf;
use App\Filament\Resources\Stafs\Pages\EditStaf;
use App\Filament\Resources\Stafs\Pages\ListStafs;
use App\Filament\Resources\Stafs\Pages\ViewStaf;
use App\Filament\Resources\Stafs\Schemas\StafForm;
use App\Filament\Resources\Stafs\Schemas\StafInfolist;
use App\Filament\Resources\Stafs\Tables\StafsTable;
use App\Models\Staf;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StafResource extends Resource
{
    protected static ?string $model = Staf::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StafForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StafInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StafsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStafs::route('/'),
            'create' => CreateStaf::route('/create'),
            'view' => ViewStaf::route('/{record}'),
            'edit' => EditStaf::route('/{record}/edit'),
        ];
    }
}
