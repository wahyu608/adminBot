<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Command Aktif', \App\Models\Command::where('status', true)->count()),
            Stat::make('Command Nonaktif', \App\Models\Command::where('status', false)->count()),
            Stat::make('Dosen', \App\Models\Dosen::count()),
            Stat::make('Staf', \App\Models\Staf::count()),
        ];
    }
}
