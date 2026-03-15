<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View; 
use Filament\Forms\Components\{
    TextInput,
    Select
};
use Illuminate\Validation\Rule;


class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
                Section::make('Informasi Jadwal Kuliah')
                    ->schema([
                        Select::make('semester')
                            ->options([
                                1 => 'Semester 1',
                                2 => 'Semester 2',
                                3 => 'Semester 3',
                                4 => 'Semester 4',
                                5 => 'Semester 5',
                                6 => 'Semester 6',
                                7 => 'Semester 7',
                                8 => 'Semester 8',
                            ])
                            ->required(),

                        TextInput::make('name')
                            ->label('Judul Jadwal Kuliah')
                            ->required(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->unique(
                                table: 'schedules',
                                column: 'slug',
                                ignoreRecord: true
                            )
                            ->helperText('URL identifier yang digunakan untuk detail jadwal kuliah, contoh : jadwal1'),
                    ]),
                Section::make('Media')
                    ->schema([
                    TextInput::make('photo')
                    ->readOnly(),
                    View::make('filament.forms.photo-preview'),
                    View::make('filament.forms.cloudinary-upload')
                        ->visible(fn ($operation) => in_array($operation, ['create', 'edit'])),
                                    ]),
            ]);
    }
}
