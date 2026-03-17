<?php

namespace App\Services\Command;

use App\Contracts\CommandServiceInterface;
use App\Models\Command;
use App\Repositories\CommandRepository;
use App\Helpers\ColumnLabelHelper;
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
        $command = strtolower(trim($command, '/'));

        $result = Cache::rememberForever(
            "command:$command",
            fn () => $this->executeInternal($command)
        );

        if (!$result) {
            return [
                'success' => false,
                'error' => 'command_not_found'
            ];
        }

        return [
            'success' => true,
            'data' => $result
        ];
    }

    private function executeInternal(string $command)
    {

        [$prefix, $sub] = $this->parseCommand($command);

        // direct command
        if ($cmd = $this->repository->findByCommand($prefix)) {
            return $this->executeCommand($cmd, $sub);
        }

        // sebagai slug detail
       if ($detail = $this->resolveDetailBySlug($prefix, $command)) {
            return $detail;
        }

        return null;
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

    private function resolveDetailBySlug(string $slug, string $command): ?array
    {
        $meta = $this->repository->resolveSlug($slug, $command);

        if (!$meta) {
            return null;
        }

        $row = $this->repository->findBySlug(
            $meta['table'],
            $meta['column'],
            $slug
        );

        if (!$row) {
            return null;
        }

        $cmd = $this->repository->findById($meta['command_id']);

        return $this->formatDetailResponse($cmd, $row, $slug);
    }

    private function formatDetailResponse(Command $cmd, object $row, string $slug): array
    {
        $textFields = collect($cmd->fields ?? [])
            ->reject(fn ($f) => $f === 'photo')
            ->values()
            ->toArray();

        $fields = collect($textFields)
            ->map(fn ($field) => ColumnLabelHelper::translate($field))
            ->toArray();

        $data = collect($row)
            ->only($textFields)
            ->mapWithKeys(fn ($value, $key) => [
                ColumnLabelHelper::translate($key) => $value
            ])
            ->toArray();

        return [
            'type' => 'detail',
            'title' => $row->name ?? $slug,
            'photo' => $row->photo
                ? (str_starts_with($row->photo, 'http')
                    ? $row->photo
                    : Storage::disk('cloudinary')->url($row->photo))
                : null,
            'data' => $data,
            'fields' => $fields,
            'response' => $cmd->response,
            'back_command' => '/' . $cmd->command,
            'has_back' => true
        ];
    }

    private function parseCommand(string $command): array
     {
         $command = trim($command, '/');
         $parts = explode('/', $command, 2);
         return [
             $parts[0] ?? null,
             $parts[1] ?? null
         ];
     }

    private function handleList(Command $cmd)
    {
        $titleMap = [
            'schedules' => 'Jadwal Kuliah',
            'dosens' => 'Dosen',
            'staffs' => 'Staf',
        ];

        $title = $titleMap[$cmd->target_table] ?? $cmd->target_table;

        $rows = $this->repository->getListData(
            $cmd->target_table,
            $cmd->target_column,
            $cmd->filters
        );

        $commands = $rows->map(fn ($row) => [
            'command' => '/' . $row->slug,
            'name' => $row->name ?? $row->{$cmd->target_column},
        ]);

        return [
            'type' => 'list',
            'title' => 'Daftar Informasi Yang Ditemukan :',
            'commands' => $commands,
        ];
    }

    private function handleText(Command $cmd)
    {
        return [
            'type' => 'text',
            'response' => $cmd->response ?? 'Belum ada respons diatur.',
            'photo' => $cmd->photo
                ? (str_starts_with($cmd->photo, 'http')
                    ? $cmd->photo
                    : Storage::disk('cloudinary')->url($cmd->photo))
                : null
        ];
    }
}
