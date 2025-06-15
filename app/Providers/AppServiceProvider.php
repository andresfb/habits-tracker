<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Tasks\Categories\CategoriesMenu;
use App\Console\Tasks\Habits\HabitsMenu;
use App\Console\Tasks\Periods\PeriodsMenu;
use App\Console\Tasks\Units\UnitsMenu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('console-tasks', fn () => collect([
            HabitsMenu::class,
            CategoriesMenu::class,
            PeriodsMenu::class,
            UnitsMenu::class,
        ]));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->configureVite();

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        } else {
            URL::forceScheme('http');
        }
    }

    /**
     * Configure the application's commands.
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            $this->app->isProduction(),
        );
    }

    /**
     * Configure the application's models.
     */
    private function configureModels(): void
    {
        Model::unguard();
        Model::shouldBeStrict();
    }

    /**
     * Configure the application's Vite instance.
     */
    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }
}
