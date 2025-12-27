<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\{CommandResolveServiceInterface,CommandServiceInterface};
Use App\Repositories\CommandRepository;
use App\Services\Command\CommandService;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CommandServiceInterface::class,
            CommandService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
