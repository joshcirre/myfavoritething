<?php

namespace App\Providers;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-feed', function (User $user, Feed $feed) {
            return $user->id === $feed->user_id;
        });
    }
}
