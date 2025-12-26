<?php

namespace App\Filament\Resources\Commands\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\{TextColumn,IconColumn};
use Filament\Tables\Table;

class CommandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('command'),
                TextColumn::make('description'),
                TextColumn::make('response'),
                TextColumn::make('type'),
                TextColumn::make('target_table'),
                TextColumn::make('target_column'),
                IconColumn::make('status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
