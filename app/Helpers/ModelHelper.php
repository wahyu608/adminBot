<?php

namespace App\Helpers;

use Illuminate\Support\Facades\{File, Cache};

class ModelHelper
{
    /**
     * Daftar tabel yang TIDAK BOLEH dijadikan sumber data bot
     */
    protected static array $excludedTables = [
        'command',                 // konfigurasi bot
        'users',                    // autentikasi
        'admins',                   // admin panel
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'failed_jobs',
        'personal_access_tokens',
    ];

    /**
     * Ambil semua model dari folder app/Models
     * lalu kembalikan array: ['nama_tabel' => 'NamaModel']
     */
    public static function getModelList(): array
    {
        return Cache::remember('model_list', 3600, function () {

            $path = app_path('Models');
            if (!File::exists($path)) return [];

            $files = File::files($path);
            $models = [];

            foreach ($files as $file) {
                $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $modelClass = "App\\Models\\$name";

                if (!class_exists($modelClass)) {
                    continue;
                }

                try {
                    $instance = new $modelClass();

                    if (!method_exists($instance, 'getTable')) {
                        continue;
                    }

                    $table = $instance->getTable();

                    if (in_array($table, self::$excludedTables, true)) {
                        continue;
                    }

                    $models[$table] = $name;

                } catch (\Throwable) {
                    continue;
                }
            }

            ksort($models);
            return $models;
        });
    }
}
