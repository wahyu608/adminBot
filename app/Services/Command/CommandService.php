<?php

namespace App\Services\Command;

use App\Contracts\CommandServiceInterface;
use App\Repositories\CommandRepository;
use App\Models\Dosen;
use App\Models\Staf;
use Illuminate\Support\Facades\Storage;


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

        $result = $this->executeInternal($command);

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
        [$prefix, $param] = $this->parseCommand($command);

        $cmd = $this->repository->findByCommand($prefix);

        if (!$cmd) {
            return [
                'success' => false,
                'error' => 'command_not_found'
            ];
        }

        if ($cmd->type === 'text') {
            return $this->handleText($cmd);
        }

        if ($cmd->type === 'list') {
            return $this->handleList($cmd->command, $param, $cmd->response);
        }

        return null;
    }

    private function handleList(string $command, ?string $slug, ?string $response)
    {
        if ($command === 'dosen') {
            return $this->handleDosen($slug, $response);
        }

        if ($command === 'staf') {
            return $this->handleStaff($slug, $response);
        }

        return null;
    }

    private function handleDosen(?string $slug, ?string $response)
    {
        if ($slug) {
            $row = Dosen::where('slug',$slug)->first();

            if (!$row) {
                return [
                    'type' => 'text',
                    'response' => 'Tidak ada dosen dengan nama tersebut. untuk melihat daftar dosen, gunakan perintah /dosen'
                ];
            }

            return [
                'type' => 'detail',
                'title' => $row->name,
                'photo' => $row->photo
                    ? (str_starts_with($row->photo, 'http')
                        ? $row->photo
                        : Storage::disk('cloudinary')->url($row->photo))
                    : null,
                'data' => [
                    'nama' => $row->name,
                    'nidn' => $row->nidn,
                    'email' => $row->email,
                    'nomor_telepon' => $row->phone_number,
                    'jabatan' => $row->position,
                    'program_studi' => $row->study_program,
                    'biodata' => $row->bio,
                ],
                'response' => $response
            ];
        }

        $rows = Dosen::select('name','slug')->get();

        return [
            'type' => 'list',
            'title' => 'Daftar Dosen',
            'commands' => $rows->map(fn ($row) => [
                'command' => '/dosen ' . $row->slug,
                'name' => $row->name,
            ]),
        ];
    }

    private function handleStaff(?string $slug, ?string $response)
    {
        if ($slug) {
            $row = Staf::where('slug',$slug)->first();

            if (!$row) {
                return [
                    'type' => 'text',
                    'response' => 'Tidak ada staf dengan nama tersebut. untuk melihat daftar staf, gunakan perintah /staf'
                ];
            }

            return [
                'type' => 'detail',
                'title' => $row->name,
                'photo' => $row->photo
                    ? (str_starts_with($row->photo, 'http')
                        ? $row->photo
                        : Storage::disk('cloudinary')->url($row->photo))
                    : null,
                'data' => [
                    'nama' => $row->name,
                    'email' => $row->email,
                    'nomor_telepon' => $row->phone_number,
                    'jabatan' => $row->position,
                    'layanan akademik' => $row->student_academic_services,

                ],
                'response' => $response
            ];
        }

        $rows = Staf::select('name','slug')->get();

        return [
            'type' => 'list',
            'title' => 'Daftar Staff',
            'commands' => $rows->map(fn ($row) => [
                'command' => '/staf ' . $row->slug,
                'name' => $row->name,
            ]),
        ];
    }

    private function handleText($cmd)
    {
        return [
        'type' =>  'text',
        'response' => $cmd->response,
        'photo' => $cmd->photo
            ? (str_starts_with($cmd->photo, 'http')
                ? $cmd->photo
                : Storage::disk('cloudinary')->url($cmd->photo))
            : null
        ];
    }

    private function parseCommand(string $command): array
    {
        $command = trim($command, '/');
        $parts = explode(' ', $command, 2);

        return [
            $parts[0] ?? null,
            $parts[1] ?? null
        ];
    }
}