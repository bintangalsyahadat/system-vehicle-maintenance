<?php

namespace App\Providers;

use App\Services\ReminderNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();
            $notifications = app(ReminderNotificationService::class)->getForUser($user);

            $view->with('webNotifications', $notifications);
            $view->with('unreadNotificationCount', $notifications->count());
        });
    }
}
