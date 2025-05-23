<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Jetstream\Events\TeamCreated;
use App\Listeners\StartTeamTrialListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TeamCreated::class => [
            StartTeamTrialListener::class,
        ],
        // Add other event listeners here if any were previously defined
        // For example, Laravel's default Registered event:
        // \Illuminate\Auth\Events\Registered::class => [
        //     \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        // ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // Set to true if you want auto-discovery, false if relying on $listen array
    }
}
