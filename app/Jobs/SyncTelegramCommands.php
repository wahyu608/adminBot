<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Events\SyncronTelegramEvent; 
class SyncTelegramCommands implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    
    protected $userId;

    public function __construct($userId = null)
    {
        $this->userId = $userId;
    }

    public function handle(): void
    {
        try {
            $url = 'http://localhost:3000/sync-commands';
            $response = Http::timeout(30)->post($url);

            if ($response->successful()) {
                Log::info('Commands Telegram berhasil disinkronkan!');
                if ($this->userId) {
                    $user = User::find($this->userId);
                    if ($user) {
                        Notification::make()
                            ->title('Sinkronisasi Selesai!')
                            ->body('Commands Telegram berhasil diperbarui.')
                            ->success()
                            ->sendToDatabase($user);

                        broadcast(new SyncronTelegramEvent($this->userId, true, 'Commands Telegram berhasil diperbarui.'));
                    }
                }
            } else {
                Log::error('Gagal sinkronisasi, status: ' . $response->status());
                
                if ($this->userId) {
                    $user = User::find($this->userId);
                    if ($user) {
                        Notification::make()
                            ->title('Sinkronisasi Gagal!')
                            ->body('Gagal dengan status: ' . $response->status())
                            ->danger()
                            ->sendToDatabase($user);
        
                        broadcast(new SyncronTelegramEvent(
                            $this->userId, 
                            false, 
                            'Gagal dengan status: ' . $response->status()
                        ));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Exception saat sinkron: ' . $e->getMessage());
            
            if ($this->userId) {
                $user = User::find($this->userId);
                if ($user) {
                    Notification::make()
                        ->title('Error Sinkronisasi!')
                        ->body('Error: ' . $e->getMessage())
                        ->danger()
                        ->sendToDatabase($user);
                        
                    broadcast(new SyncronTelegramEvent(
                        $this->userId, 
                        false, 
                        'Error: ' . $e->getMessage()
                    ));
                }
            }
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job SyncTelegramCommands failed after ' . $this->tries . ' attempts: ' . $exception->getMessage());
        
        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                Notification::make()
                    ->title('Sinkronisasi Gagal Total!')
                    ->body('Job gagal setelah ' . $this->tries . ' percobaan.')
                    ->danger()
                    ->sendToDatabase($user);
                    
                broadcast(new SyncronTelegramEvent(
                    $this->userId, 
                    false, 
                    'Job gagal setelah ' . $this->tries . ' percobaan.'
                ));
            }
        }
    }
}