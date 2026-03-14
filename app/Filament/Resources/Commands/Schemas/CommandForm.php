<?php

namespace App\Filament\Resources\Commands\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Forms\Components\{
    TextInput,
    Toggle,
    Select,
    MultiSelect,
    Textarea,
};
use App\Helpers\ModelHelper;
use App\Helpers\ColumnLabelHelper;
use Illuminate\Support\Facades\Schema as SchemaHelper;

class CommandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
           Section::make('Informasi Command')
                ->description('Detail utama command yang akan digunakan bot')
                ->schema([
                    TextInput::make('command')
                       ->label('Nama Command')
                        ->helperText('contoh : dosen, staf, jadwal_kuliah, hanya huruf kecil.')
                        ->required()
                        ->rule('regex:/^[a-z0-9_]+$/')
                        ->unique(
                            table: 'command',
                            column: 'command',
                            ignoreRecord: true
                        ),

                    TextInput::make('description')
                         ->label('Deskripsi Command')
                        ->helperText('Penjelasan singkat fungsi command')
                        ->required(),

                    Select::make('type')
                        ->label('Jenis Respon Bot')
                        ->options([
                            'text' => 'Teks Statis',
                            'list' => 'Daftar Data',
                        ])
                        ->default('text')
                        ->required(),

                    Toggle::make('status')
                        ->label('Status Command')
                        ->helperText('Aktifkan agar command dapat digunakan oleh bot')
                        ->default(true),

                ]),

                Section::make('Respon Bot')
                ->description('Pesan yang dikirim bot kepada pengguna')
                ->schema([

                    Textarea::make('response')
                        ->label('Pesan Respon')
                        ->rows(4)
                        ->helperText(
                            'Pesan naratif bot. 
                            Digunakan sebagai respon utama (teks statis), contoh: "Halo, ini informasi yang Anda minta: ...". 
                            atau sebagai pesan pengantar/detail (daftar data), contoh: "Berikut detail dari data yang Anda pilih: ..."'
                        )
                        ->required(),
                ]),
                Section::make('Media')
                ->description('Foto atau media yang dikirim bot bersama respon (optional)')
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
