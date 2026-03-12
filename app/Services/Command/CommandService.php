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
        $command = strtolower(trim($command, '/'));
        return Cache::rememberForever(
            "command:$command",
            fn () => $this->executeInternal($command)
        );
    }

    private function executeInternal(string $command)
    {

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
        $meta = $this->repository->resolveSlug($slug);

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
        $fieldMap = [
            'name' => 'nama',
            'nidn' => 'nidn',
            'email' => 'email',
            'phone_number' => 'nomor_telepon',
            'position' => 'jabatan',
            'study_program' => 'program_studi',
            'bio' => 'biografi',
            'student_academic_services' => 'layanan_akademik_mahasiswa',
        ];

        dd($cmd->fields);

        $textFields = collect($cmd->fields ?? [])
            ->reject(fn ($f) => $f === 'photo')
            ->values()
            ->toArray();

        $fields = collect($textFields)
            ->map(fn ($field) => $fieldMap[$field] ?? $field)
            ->toArray();

        $data = collect($row)
            ->only($textFields)
            ->mapWithKeys(fn ($value, $key) => [
                $fieldMap[$key] ?? $key => $value
            ])
            ->toArray();

        return [
            'type' => 'detail',
            'title' => $slug,
            'photo' => $row->photo
                ? (str_starts_with($row->photo, 'http')
                    ? $row->photo
                    : Storage::disk('cloudinary')->url($row->photo))
                : null,
            'data' => $data,
            'fields' => $fields,
            'response' => $cmd->response,
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
        $rows = $this->repository->getListData(
            $cmd->target_table,
            $cmd->target_column
        );

        $commands = $rows->map(fn ($row) => [
            'command' => '/' . $row->slug,
            'name' => $row->name,
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
