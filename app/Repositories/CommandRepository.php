<?php

namespace App\Repositories;

use App\Models\Command;
use Illuminate\Support\Facades\DB;

class CommandRepository
{
    public function getActive()
    {
        return Command::where('status', true)
            ->get(['command','description','response','type']);
    }

    public function findByCommand(string $command): ?Command
    {
        return Command::where('command',$command)->first();
    }
}

