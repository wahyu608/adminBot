<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\{CommandResolveServiceInterface,CommandServiceInterface};
Use App\Repositories\CommandRepository;
use App\Services\Command\CommandService;
use App\Models\{Dosen,Staf,Command};
use App\Observers\{DosenObserver,StafObserver,CommandObserver};
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(
            CommandServiceInterface::class,
            CommandService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
        \URL::forceScheme('https');
        }
        Command::observe(CommandObserver::class);
        Dosen::observe(DosenObserver::class);
        Staf::observe(StafObserver::class);
    }
}
