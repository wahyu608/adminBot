<?php

namespace App\Repositories;

use App\Models\Command;


class CommandRepository
{
    public function getActive()
    {
        return Command::where('status', true)
            ->get(['command', 'description', 'response', 'type', 'target_table', 'target_column', 'fields', 'status']);
    }

    public function findByCommand(string $command): ?Command
    {
        return Command::where('command', $command)->first();
    }
}

