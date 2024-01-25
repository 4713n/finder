<?php

namespace link0\Finder\Listeners;

use link0\Finder\Events\SearchResultFoundEvent;
use link0\Finder\Events\SearchResultFoundBroadcastEvent;

class SearchResultFoundListener {
    public function handle(SearchResultFoundEvent $event) {
        $broadcastMethod = config('finder.broadcasting.method');

        if ($broadcastMethod === 'websockets') {
			broadcast(new SearchResultFoundBroadcastEvent($event->type, $event->message, $event->data));
        }
    }
}
