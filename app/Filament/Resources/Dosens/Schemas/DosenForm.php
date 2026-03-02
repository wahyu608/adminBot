<?php

namespace App\Filament\Resources\Dosens\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Forms\Components\{
    TextInput,
    Textarea,
    FileUpload,
};
use Illuminate\Support\Str;

class DosenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas Dosen')
                ->description('Informasi utama dan identitas unik dosen')
                ->columns(2)
                ->schema([

                    TextInput::make('name')
                        ->label('Nama Dosen')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(
                            fn (callable $set, $state) =>
                                $set('slug', Str::slug($state, '_')),
                        ),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->disabled()
                        ->dehydrated(true),

                    TextInput::make('nidn')
                        ->label('NIDN')
                        ->numeric()
                        ->unique(ignoreRecord: true),

                    TextInput::make('position')
                        ->label('Jabatan'),

                    TextInput::make('study_program')
                        ->label('Program Studi'),
                ]),

            Section::make('Kontak & Profil')
                ->columns(2)
                ->schema([

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->unique(ignoreRecord: true),

                    TextInput::make('phone_number')
                        ->label('Nomor Telepon'),

                    Textarea::make('description')
                        ->label('Deskripsi Dosen')
                        ->helperText('Deskripsi singkat untuk profil atau informasi tambahan')
                        ->columnSpanFull(),
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
