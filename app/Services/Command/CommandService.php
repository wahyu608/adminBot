<?php

namespace App\Services\Command;

use App\Contracts\CommandServiceInterface;
use App\Models\Command;
use App\Repositories\CommandRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Log;


class CommandService implements CommandServiceInterface
{
    public function __construct(
        protected CommandRepository $repository
    ) {}

    public function getActiveCommands()
    {
        return $this->repository->getActive();
    }

    public function execute(string $command)
    {
        return Cache::remember(
            "command:$command",
            300,
            fn () => $this->executeInternal($command)
        );
    }

    private function executeInternal(string $command)
    {   
        Log::info('EXECUTE INTERNAL HIT', [
            'command' => $command,
        ]);

        [$prefix, $sub] = $this->parseCommand($command);

        $cmd = $this->repository->findByCommand($prefix);
        
        if ($cmd) {
            if ($sub) {
                if ($cmd->type !== 'list') {
                    abort(400, "Command {$cmd->command} tidak mendukung sub-command");
                }
                return $this->handleDetail($cmd, $sub);
            }

            return match ($cmd->type) {
                'list' => $this->handleList($cmd),
                'text' => $this->handleText($cmd),
                default => abort(400, 'Unknown command type')
            };
        }

        foreach ($this->repository->getListCommands() as $listCmd) {
            $result = $this->tryResolveDetail($listCmd, $prefix);
            if ($result) {
                return $result;
            }
        }

        abort(404, 'Command not found');
    }

    private function tryResolveDetail(Command $cmd, string $slug): ?array
    {
        $column = $cmd->target_column;

        $row = DB::table($cmd->target_table)
            ->where($column, $slug)
            ->first();

        if (!$row) {
            return null;
        }

        $textFields = collect($cmd->fields)
            ->reject(fn ($f) => $f === 'photo')
            ->values()
            ->toArray();

        return [
            'type' => 'detail',
            'title' => $slug,
            'photo' => $row->photo
                ? Storage::disk('cloudinary')->url($row->photo)
                : null,
            'data' => collect($row)->only($textFields)->toArray(),
            'fields' => $textFields,
            'response' => $cmd->response,
        ];
    }


    private function parseCommand(string $command): array
    {
        $parts = explode('/', ltrim($command, '/'));
        return [$parts[0] ?? null, $parts[1] ?? null];
    }

    private function handleList(Command $cmd)
    {
        $column = $cmd->target_column ?? 'name';

        $rows = DB::table($cmd->target_table)
            ->select($column)
            ->get();

        $commands = $rows->map(fn ($row) => [
            'command' => '/' . strtolower(str_replace(' ', '_', $row->$column)),
            'description' => $row->$column,
        ]);

        return [
            'type' => 'list',
            'title' => 'Daftar ' . ucfirst($cmd->target_table),
            'commands' => $commands,
        ];
    }

    private function handleDetail(Command $cmd, string $sub)
    {
       
        if (!$cmd->target_table || !$cmd->target_column) {
        abort(500, 'Command belum dikonfigurasi dengan benar (target_table / target_column kosong)');
    }

        $column = $cmd->target_column ?? 'name';
        $name = strtolower(str_replace('_', ' ', $sub));

        $row = DB::table($cmd->target_table)
            ->whereRaw("LOWER($column) = ?", [$name])
            ->first();

        if (!$row) {
            abort(404, "$name not found");
        }

        $textFields = collect($cmd->fields)
            ->reject(fn ($f) => $f === 'photo')
            ->values()
            ->toArray();

        return [
            'type' => 'detail',
            'title' => $name,
            'photo' => $row->photo
                ? Storage::disk('cloudinary')->url($row->photo) : null,
            'data' => collect($row)
                ->only($textFields)
                ->toArray(),
            'fields' => $textFields,
            'response' => $cmd->response,
        ];
    }


    private function handleText(Command $cmd)
    {
        return [
            'type' => 'text',
            'response' => $cmd->response ?? 'Belum ada respons diatur.',
        ];
    }
}
