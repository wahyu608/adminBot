<?php

namespace App\Contracts;

interface CommandServiceInterface
{
    public function getActiveCommands();
    public function execute(string $command);
}

