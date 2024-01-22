<?php

namespace link0\Finder\Listeners;

use link0\Finder\Events\SearchResultFoundEvent;
use link0\Finder\Events\SearchResultFoundBroadcastEvent;

class SearchResultFoundListener {
    public function handle(SearchResultFoundEvent $event) {
        $broadcastMethod = config('finder.broadcast_method');

        if ($broadcastMethod === 'websockets') {
            event(new SearchResultFoundBroadcastEvent($event->type, $event->message, $event->data));
        }
    }
}
