<?php

namespace link0\Finder\Listeners;

use link0\Finder\Events\SearchStartedEvent;
use link0\Finder\Events\SearchStartedBroadcastEvent;

class SearchStartedListener {
    public function handle(SearchStartedEvent $event) {
        $broadcastMethod = config('finder.broadcasting.method');

        if ($broadcastMethod === 'websockets') {
            broadcast(new SearchStartedBroadcastEvent($event->searchId, $event->data));
        }
    }
}
