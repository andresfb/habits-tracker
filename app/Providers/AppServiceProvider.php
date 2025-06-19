<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Tasks\Categories\CategoriesMenu;
use App\Console\Tasks\Habits\HabitsMenu;
use App\Console\Tasks\Invitations\InvitationsMenu;
use App\Console\Tasks\Periods\PeriodsMenu;
use App\Console\Tasks\Units\UnitsMenu;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->app->bind('habits-tasks', fn () => collect([
            HabitsMenu::class,
            CategoriesMenu::class,
            PeriodsMenu::class,
            UnitsMenu::class,
        ]));

        $this->app->bind('invite-tasks', fn () => collect([
            InvitationsMenu::class,
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

        RateLimiter::for('login', static fn (Request $request): array => [
            Limit::perMinute(50),
            Limit::perMinute(5)->by($request->input('email')),
        ]);

        RateLimiter::for('invite', static fn (Request $request): array => [
            Limit::perMinute(20),
            Limit::perMinute(3)->by($request->input('token')),
        ]);
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
