<?php

namespace Link000\Finder\Listeners;

use Link000\Finder\Events\SearchStartedEvent;
use Link000\Finder\Events\SearchStartedBroadcastEvent;

class SearchStartedListener {
    public function handle(SearchStartedEvent $event) {
        $broadcastMethod = config('finder.broadcasting.method');

        if ($broadcastMethod === 'websockets') {
            broadcast(new SearchStartedBroadcastEvent($event->searchId, $event->data));
        }
    }
}
