<?php

namespace App\Filament\Resources\Commands\Tables;

use Filament\Actions\{
    BulkActionGroup,
    DeleteBulkAction,
    EditAction,
    ViewAction
};
use Filament\Tables\Columns\{
    TextColumn,
    IconColumn,
    BadgeColumn
};
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()

            ->columns([

                TextColumn::make('command')
                    ->label('Command')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->wrap()
                    ->limit(50),

                TextColumn::make('response')
                    ->label('Pesan Respon')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->response),

                BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'text',
                        'warning' => 'list',
                    ])
                    ->formatStateUsing(fn ($state) =>
                        $state === 'text'
                            ? 'Teks'
                            : 'Daftar Data'
                    ),

                TextColumn::make('target_table')
                    ->label('Sumber Data')
                    ->formatStateUsing(fn ($state) =>
                        $state ? ucfirst($state) : '-'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

            ])

            ->filters([

                SelectFilter::make('type')
                    ->label('Tipe Command')
                    ->options([
                        'text' => 'Teks',
                        'list' => 'Daftar Data',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Nonaktif',
                    ]),

            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
