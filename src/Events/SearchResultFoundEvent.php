<?php

namespace link0\Finder\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SearchResultFoundEvent {
    use Dispatchable, SerializesModels;

	public string $type;
	public string $message;
	public array $data;

    public function __construct(string $type, string $message, array $data = []) {
        $this->type = $type;
        $this->message = $message;
        $this->data = $data;
    }
}