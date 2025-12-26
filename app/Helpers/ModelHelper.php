<?php

namespace App\Helpers;

use Illuminate\Support\Facades\{File,Cache};


class ModelHelper
{
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

                if (!class_exists($modelClass)) continue;

                try {
                    $instance = new $modelClass();
                    if (method_exists($instance, 'getTable')) {
                        $table = $instance->getTable();
                        if (in_array($table, ['users', 'migrations', 'password_resets'])) {
                            continue;
                        }
                        $models[$table] = $name; 
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }

            ksort($models);
            return $models;
        });
    }
}
