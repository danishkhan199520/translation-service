<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TranslationRepository;
use App\Services\TranslationService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TranslationRepository::class);
        $this->app->singleton(TranslationService::class);
    }

    public function boot(): void
    {
        //
    }
}
