<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\SystemStats;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use App\Jobs\SyncTelegramCommands;

class CustomDashboard extends BaseDashboard
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';
    
    public function getWidgets(): array
    {
        return [
            SystemStats::class,
        ];
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncTelegram')
                ->label('Sync Telegram Commands')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    SyncTelegramCommands::dispatch(auth()->id());

                    Notification::make()
                        ->title('🔄 Sinkronisasi dimulai...')
                        ->body('Status akan update otomatis')
                        ->success()
                        ->send();
                }),
        ];
    }
}