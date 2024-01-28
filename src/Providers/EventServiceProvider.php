<?php

namespace link0\Finder\Providers;

use link0\Finder\Events\SearchResultFoundEvent;
use link0\Finder\Listeners\SearchResultFoundListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use link0\Finder\Events\SearchStartedEvent;
use link0\Finder\Listeners\SearchStartedListener;

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