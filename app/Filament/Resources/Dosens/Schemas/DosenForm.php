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
use Illuminate\Validation\Rule;
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
                                $set('slug', Str::slug($state)),
                        ),

                    TextInput::make('slug')
                        ->label('Slug Command')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->regex('/^[a-z0-9-]+$/')
                        ->helperText('Digunakan untuk command bot dan harus unik. Contoh: wahyu'),

                    TextInput::make('nidn')
                        ->label('NIDN')
                        ->numeric()
                        ->rules(fn ($record) => [
                            Rule::unique('dosens', 'nidn')->ignore($record?->id)
                        ]),

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

                    Textarea::make('bio')
                        ->label('Biodata')
                        ->helperText('Deskripsi singkat tentang dosen, bidang keahlian, dan informasi relevan lainnya.')
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
