<?php

namespace Link000\Finder\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class BroadcastNowEvent implements ShouldBroadcastNow {
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public string $type;
	public string $message;
	public array $data;

	/**
	 * Create broadcast event
	 *
	 * @param string $type
	 * @param string $message
	 * @param array $data
	 */
	public function __construct(string $type, string $message, array $data = []) {
		$this->type = $type;
		$this->message = $message;
		$this->data = $data;
	}
}
