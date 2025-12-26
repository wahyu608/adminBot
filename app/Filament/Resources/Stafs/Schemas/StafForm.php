<?php

namespace App\Filament\Resources\Stafs\Schemas;

use Filament\Forms\Components\{FileUpload,Textarea,TextInput};
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class StafForm
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
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('phone_number')
                    ->tel()
                    ->default(null),
                TextInput::make('position')
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
