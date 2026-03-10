<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class VerifyEmailNotice extends Page
{
    protected string $view = 'filament.pages.verify-email-notice';

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static bool $shouldRegisterNavigation = false;

    
    public function getHeading(): string
    {
        return '';
    }

    public function mount(): void
        {
            if (Auth::user()->hasVerifiedEmail()) {
                redirect()->route('filament.admin.pages.custom-dashboard');
            }
        }

    public function resend()
    {
        auth()->user()->sendEmailVerificationNotification();

        Notification::make()
            ->title('Email verifikasi telah dikirim')
            ->body('Silakan cek inbox atau folder spam.')
            ->success()
            ->send();
    }
}
