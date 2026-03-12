<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\CommandServiceInterface;

class CommandController extends Controller
{
    public function __construct(
        protected CommandServiceInterface $commandService
    ) {}

    public function index()
    {   
        abort(500);
        return response()->json(
            $this->commandService->getActiveCommands()
        );
    }

    public function execute(Request $request, string $command)
    {
        abort(500);
        return response()->json(
            $this->commandService->execute($command)
        );
    }
}
