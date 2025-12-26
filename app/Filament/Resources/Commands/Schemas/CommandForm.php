<?php

namespace App\Filament\Resources\Commands\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\{TextInput,Toggle,Select,MultiSelect, TextArea};
use App\Helpers\ModelHelper;
use Illuminate\Support\Facades\Schema as SchemaHelper;



class CommandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('command')
                    ->required(),
                TextInput::make('description')
                    ->required(),
                TextArea::make('response'),
                Select::make('type')
                    ->options([
                        'text' => 'Text',
                        'list' => 'List',
                    ])
                    ->default('text')
                    ->reactive()
                    ->required(),
                Select::make('target_table')
                    ->label('Target Tabel / Model')
                    ->options(ModelHelper::getModelList())
                    ->searchable()
                    ->placeholder('Pilih model tujuan (opsional)')
                    ->helperText('Model ini akan digunakan untuk mengambil data sesuai command/perintah')
                    ->columnSpanFull()
                    ->reactive()
                    ->hidden(fn (callable $get) => $get('type') !== 'list')
                    ->afterStateUpdated(function (callable $set) {
                        $set('target_column', null);
                    }),
                    
                Select::make('target_column')
                    ->label('Target Field')
                    ->options(function (callable $get) {
                        $table = $get('target_table');
                        if (!$table) return ["1"];

                        try {
                            $columns = SchemaHelper::getColumnListing($table);
                            return collect($columns)
                                ->filter(fn ($col) => !in_array($col, ['id', 'created_at', 'updated_at']))
                                ->mapWithKeys(fn ($col) => [$col => ucfirst(str_replace('_', ' ', $col))])
                                ->toArray();
                        } catch (\Throwable $e) {
                            return [];
                        }
                    })
                    ->reactive()
                    ->hidden(fn (callable $get) => !$get('target_table')),
                MultiSelect::make('fields')
                    ->label('Columns to Display in Bot')
                    ->options(function (callable $get) {
                        $table = $get('target_table');
                        if (!$table) return [];

                        try {
                            $columns = SchemaHelper::getColumnListing($table);
                            return collect($columns)
                                ->filter(fn($col) => !in_array($col, ['id', 'created_at', 'updated_at']))
                                ->mapWithKeys(fn($col) => [$col => ucfirst(str_replace('_', ' ', $col))])
                                ->toArray();
                        } catch (\Throwable $e) {
                            return [];
                        }
                    })
                    ->columns(2)
                    ->reactive()
                    ->hidden(fn (callable $get) => $get('type') !== 'list')
                    ->afterStateHydrated(function ($state, callable $set, callable $get) {
                        if (empty($state)) {
                            $table = $get('target_table');
                            if (!$table) return;
                            try {
                                $columns = SchemaHelper::getColumnListing($table);
                                $defaultFields = collect($columns)
                                    ->filter(fn($col) => !in_array($col, ['id', 'created_at', 'updated_at']))
                                    ->values()
                                    ->toArray();
                                $set('fields', $defaultFields);
                            } catch (\Throwable $e) {
                                return;
                            }
                        }
                    })
                    ->helperText('Pilih kolom mana saja yang akan ditampilkan di bot'),
                Toggle::make('status'),
            ]);
    }
}
