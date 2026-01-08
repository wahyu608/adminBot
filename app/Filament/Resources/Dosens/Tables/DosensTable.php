<?php

namespace App\Filament\Resources\Dosens\Tables;

use Filament\Actions\{
    BulkActionGroup,
    DeleteBulkAction,
    EditAction,
    ViewAction
};
use Filament\Tables\Columns\{
    ImageColumn,
    TextColumn
};
use Filament\Tables\Table;

class DosensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()

            ->columns([

                ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('cloudinary')
                    ->circular()
                    ->size(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Nama Dosen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('nidn')
                    ->label('NIDN')
                    ->searchable(),

                TextColumn::make('study_program')
                    ->label('Program Studi')
                    ->searchable(),

                TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone_number')
                    ->label('No. Telepon')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
            ])

            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
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
