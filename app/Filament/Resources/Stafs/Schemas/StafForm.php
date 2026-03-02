<?php

namespace App\Filament\Resources\Stafs\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Forms\Components\{
    TextInput,
    Textarea,
    FileUpload
};
use Illuminate\Support\Str;

class StafForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas Staf')
                ->description('Informasi utama dan identitas unik staf')
                ->columns(2)
                ->schema([

                    TextInput::make('name')
                        ->label('Nama Staf')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(
                            fn (callable $set, $state) =>
                                $set('slug', Str::slug($state, '_'))
                        ),

                        
                        TextInput::make('position')
                        ->label('Jabatan'),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->disabled()
                            ->dehydrated(true),
                ]),
            Section::make('Kontak & Profil')
                ->columns(2)
                ->schema([

                    TextInput::make('email')
                        ->label('Email')
                        ->email(),

                    TextInput::make('phone_number')
                        ->label('Nomor Telepon')
                        ->tel(),

                    Textarea::make('description')
                        ->label('Deskripsi Staf')
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
