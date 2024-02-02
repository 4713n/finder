<?php

namespace Link000\Finder\Providers;

use Link000\Finder\Events\SearchResultFoundEvent;
use Link000\Finder\Listeners\SearchResultFoundListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Link000\Finder\Events\SearchStartedEvent;
use Link000\Finder\Listeners\SearchStartedListener;

class EventServiceProvider extends ServiceProvider {
	protected $listen = [
        SearchResultFoundEvent::class => [
            SearchResultFoundListener::class,
        ],

        SearchStartedEvent::class => [
            SearchStartedListener::class,
        ],
    ];

	/**
     * Register events
     *
     * @return void
     */
    public function boot() {
        parent::boot();
    }
}