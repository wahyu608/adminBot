<?php

namespace App\Repositories;

use App\Models\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CommandRepository
{
    public function getSlugIndex(): array
    {
        return Cache::rememberForever('slug_index', function () {

            $map = [];

            $commands = $this->getListCommands();

            foreach ($commands as $cmd) {

                $rows = DB::table($cmd->target_table)
                    ->select('slug')
                    ->get();

                foreach ($rows as $row) {

                    $map[$row->slug] = [
                        'table' => $cmd->target_table,
                        'column' => $cmd->target_column,
                        'command_id' => $cmd->id
                    ];
                }
            }

            return $map;
        });
    }
    public function resolveSlug(string $slug): ?array
    {
        $index = $this->getSlugIndex();

        return $index[$slug] ?? null;
    }
    public function findById(int $id): ?Command
    {
        return Command::find($id);
    }
    public function getActive()
    {
        return Command::where('status', true)
            ->get(['command', 'description', 'response', 'type', 'target_table', 'target_column', 'fields', 'status']);
    }

    public function findByCommand(string $command): ?Command
    {
        return Command::where('command', $command)->first();
    }

    public function getListCommands()
    {
        return Command::where('type', 'list')
            ->whereNotNull('target_table')
            ->whereNotNull('target_column')
            ->get();
    }
    public function findBySlug(string $table, string $column, string $slug)
    {
        return DB::table($table)
            ->where($column, $slug)
            ->first();
    }
    public function getListData(string $table, string $targetColumn)
    {
        return DB::table($table)
            ->select(['slug', 'name', $targetColumn])
            ->get();
    }

}

