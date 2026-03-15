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
    Repeater,
    Hidden,
};
use App\Helpers\ModelHelper;
use App\Helpers\ColumnLabelHelper;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Schema as SchemaHelper;


class CommandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Command')
                ->description('Identitas dan deskripsi dasar command bot')
                ->columns(2)
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
                        ->required()
                        ->reactive(),

                    Toggle::make('status')
                        ->label('Status Command')
                        ->helperText('Aktifkan agar command dapat digunakan oleh bot')
                        ->default(true),
                ]),

            Section::make('Pesan Respon Bot')
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
                ->description('Foto yang dikirim bot bersama respon (optional)')
                ->schema([
                    TextInput::make('photo')
                    ->readOnly(),
                    View::make('filament.forms.photo-preview'),
                    View::make('filament.forms.cloudinary-upload')
                        ->visible(fn ($operation) => in_array($operation, ['create', 'edit'])),
                                    ]),

            Section::make('Konfigurasi Data')
                ->description('Pengaturan sumber data untuk command bertipe daftar')
                ->visible(fn (callable $get) => $get('type') === 'list')        
                ->schema([
                    Select::make('target_table')
                        ->label('Sumber Data (Tabel)')
                        ->required(fn (callable $get) => $get('type') === 'list')
                        ->options(ModelHelper::getModelList())
                        ->searchable()
                        ->placeholder('Pilih tabel sumber data')
                        ->helperText('Data akan diambil dari tabel ini')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set) {
                            $set('target_column', 'slug');
                        }),

                    TextInput::make('target_column')
                        ->label('Kolom Utama')
                        ->required( fn (callable $get) => $get('type') === 'list')    
                        ->helperText('Kolom referensi utama data (ditetapkan otomatis oleh sistem)')
                        ->disabled()
                        ->dehydrated(true)
                        ->visible(function (callable $get) {
                            $table = $get('target_table');
                            if (!$table) return false;

                            try {
                                return in_array('slug', SchemaHelper::getColumnListing($table));
                            } catch (\Throwable) {
                                return false;
                            }
                        }),


                    MultiSelect::make('fields')
                        ->placeholder('Pilih informasi yang akan ditampilkan')
                        ->label('informasi yang Ditampilkan')
                        ->helperText('informasi data yang akan ditampilkan oleh bot')
                        ->required(fn (callable $get) => $get('type') === 'list')
                        ->columns(2)
                        ->options(function (callable $get) {
                            $table = $get('target_table');
                            if (!$table) return [];

                            try {
                                return collect(SchemaHelper::getColumnListing($table))
                                    ->reject(fn ($col) => in_array($col, ['id','slug', 'created_at', 'updated_at']))
                                    ->mapWithKeys(fn ($col) => [
                                        $col => ColumnLabelHelper::translate($col)
                                    ])
                                    ->toArray();
                            } catch (\Throwable) {
                                return [];
                            }
                        }),

                        Select::make('filter_column')
                            ->label('Kolom Filter')
                            ->options(function (Get $get) {

                                $table = $get('target_table');

                                if (!$table) return [];

                                try {
                                    return collect(SchemaHelper::getColumnListing($table))
                                        ->reject(fn ($col) => in_array($col, ['id','slug','created_at','updated_at']))
                                        ->mapWithKeys(fn ($col) => [
                                            $col => ColumnLabelHelper::translate($col)
                                        ])
                                        ->toArray();
                                } catch (\Throwable) {
                                    return [];
                                }
                            })
                            ->searchable()
                            ->live()
                            ->dehydrated(false),

                        Select::make('filter_value')
                            ->label('Nilai Filter')
                            ->helperText('Gunakan Filter jika ingin menampilkan data tertentu')
                            ->options(function (Get $get) {

                                $table = $get('target_table');
                                $column = $get('filter_column');

                                if (!$table || !$column) return [];

                                try {
                                    return \DB::table($table)
                                        ->distinct()
                                        ->pluck($column, $column)
                                        ->toArray();
                                } catch (\Throwable) {
                                    return [];
                                }
                            })
                            ->searchable()
                            ->dehydrated(false),
                        Hidden::make('filters')
                            ->dehydrateStateUsing(function (Get $get) {

                                $column = $get('filter_column');
                                $value  = $get('filter_value');

                                if (!$column || $value === null) {
                                    return null;
                                }

                                return [
                                    [
                                        'column' => $column,
                                        'value' => $value,
                                    ]
                                ];
                            }),
                ]),
        ]);
    }
}
