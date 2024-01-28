<?php

namespace link0\Finder\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SearchStartedEvent {
    use Dispatchable, SerializesModels;

	public string $searchId;
	public array $data;

    public function __construct(string $searchId, array $data = []) {
        $this->searchId = $searchId;
        $this->data = $data;
    }
}