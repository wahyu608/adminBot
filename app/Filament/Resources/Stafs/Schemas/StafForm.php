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
                        ->required(),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->unique(
                            table: 'stafs',
                            column: 'slug',
                            ignoreRecord: true
                        )
                        ->helperText('URL identifier yang digunakan untuk detail staf, contoh : wahyu'),
                        
                        TextInput::make('position')
                        ->label('Jabatan'),
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

                    Textarea::make('student_academic_services')
                        ->label('Layanan Akademik Mahasiswa')
                        ->helperText('Deskripsi singkat tentang layanan akademik yang diberikan oleh staf, seperti konsultasi akademik, bantuan administrasi, atau layanan lainnya.')
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
