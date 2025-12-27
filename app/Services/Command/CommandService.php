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
        Log::info('EXECUTE INTERNAL HIT', ['command' => $command]);

        [$prefix, $sub] = $this->parseCommand($command);

        // direct command
        if ($cmd = $this->repository->findByCommand($prefix)) {
            return $this->executeCommand($cmd, $sub);
        }

        // sebagai slug detail
        if ($detail = $this->resolveDetailBySlug($prefix)) {
            return $detail;
        }

        abort(404, 'Command not found');
    }

    private function executeCommand(Command $cmd, ?string $sub)
    {
        if ($sub) {
            abort(400, "Command {$cmd->command} tidak mendukung sub-command");
        }

        return match ($cmd->type) {
            'list' => $this->handleList($cmd),
            'text' => $this->handleText($cmd),
            default => abort(400, 'Unknown command type')
        };
    }

    private function resolveDetailBySlug(string $slug): ?array
    {
        foreach ($this->repository->getListCommands() as $cmd) {
            $row = DB::table($cmd->target_table)
                ->where($cmd->target_column, $slug)
                ->first();

            if ($row) {
                return $this->formatDetailResponse($cmd, $row, $slug);
            }
        }

        return null;
    }

    private function formatDetailResponse(Command $cmd, object $row, string $slug): array
    {
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
        $rows = DB::table($cmd->target_table)
            ->select(['slug', $cmd->target_column])
            ->get();

        $commands = $rows->map(fn ($row) => [
            'command' => '/' . $row->slug,
            'description' => $row->{$cmd->target_column},
        ]);

        return [
            'type' => 'list',
            'title' => 'Daftar ' . ucfirst($cmd->target_table),
            'commands' => $commands,
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
