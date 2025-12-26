<?php

namespace App\Filament\Resources\Dosens\Schemas;

use Filament\Forms\Components\{FileUpload,TextInput};
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Filament\Forms\Set;


class DosenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true) 
                    ->afterStateUpdated(function ( $set, $state) { $set('slug', Str::slug($state, '_')); }),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true) 
                    ->disabled() 
                    ->dehydrated(), 
                TextInput::make('nidn')
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('phone_number')
                    ->default(null),
                TextInput::make('position')
                    ->default(null),
                TextInput::make('study_program')
                    ->default(null),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('photo')
                    ->disk('cloudinary')
                    ->directory('emillia')
                    ->image(),
            ]);
    }
}
