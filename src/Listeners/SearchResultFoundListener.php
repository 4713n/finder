<?php

namespace link0\Finder\Listeners;

use link0\Finder\Events\SearchResultFoundEvent;
use link0\Finder\Events\SearchResultFoundBroadcastEvent;

class SearchResultFoundListener {
    public function handle(SearchResultFoundEvent $event) {
        $broadcastMethod = config('finder.broadcasting.method');

        if ($broadcastMethod === 'websockets') {
            foreach($event->data as $result){
                if( empty($result) ) continue;

                broadcast(new SearchResultFoundBroadcastEvent($event->type, $result, ['parsed' => $this->getParsedResult($result)]));
            }
        }
    }

    private function getParsedResult($result): array {
        $lastColonPosition = strrpos($result, ':');
        
        $file = substr($result, 0, $lastColonPosition ?: null);
       
        $lastDotPosition = strrpos($file, '.');

        $filename = substr($file, 0, $lastDotPosition ?: null);

        $extension = $lastDotPosition !== false ? substr($file, $lastDotPosition + 1) : '';
        $matchesCount = $lastColonPosition !== false ? (int)substr($result, $lastColonPosition + 1) : '';

        return [
            'file'              => $filename,
            'extension'         => $extension,
            'matchesCount'      => $matchesCount,
        ];
    }
}
