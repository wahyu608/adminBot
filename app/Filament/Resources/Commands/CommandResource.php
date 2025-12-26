<?php

namespace App\Filament\Resources\Commands;

use App\Filament\Resources\Commands\Pages\CreateCommand;
use App\Filament\Resources\Commands\Pages\EditCommand;
use App\Filament\Resources\Commands\Pages\ListCommands;
use App\Filament\Resources\Commands\Pages\ViewCommand;
use App\Filament\Resources\Commands\Schemas\CommandForm;
use App\Filament\Resources\Commands\Schemas\CommandInfolist;
use App\Filament\Resources\Commands\Tables\CommandsTable;
use App\Models\Command;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommandResource extends Resource
{
    protected static ?string $model = Command::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Perintah';

    public static function form(Schema $schema): Schema
    {
        return CommandForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommandInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommandsTable::configure($table);
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
            'index' => ListCommands::route('/'),
            'create' => CreateCommand::route('/create'),
            'view' => ViewCommand::route('/{record}'),
            'edit' => EditCommand::route('/{record}/edit'),
        ];
    }
}
