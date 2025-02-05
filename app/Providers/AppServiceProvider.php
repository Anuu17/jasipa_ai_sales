<?php

namespace App\Providers;

use App\Services\ClaudeService;
use App\Services\TokenCounterService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ClaudeService::class, function ($app) {
            return new ClaudeService();
        });
        $this->app->singleton(TokenCounterService::class, function ($app) {
            return new TokenCounterService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
