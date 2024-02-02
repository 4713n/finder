<?php

namespace Link000\Finder\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Auth;
use Link000\Finder\Events\BroadcastNowEvent;
use Illuminate\Broadcasting\PrivateChannel;

class SearchStartedBroadcastEvent extends BroadcastNowEvent {

	/**
	 * Create broadcast event
	 */
	public function __construct(string $searchId, array $data = []) {
		parent::__construct('info', $searchId, $data); 
	}

	/**
	 * The event's broadcast name
	 */
	public function broadcastAs(): string {
		$broadcastName = 'App\Events\SearchStartedBroadcastEvent';

		return $broadcastName;
	}

	/**
	 * Get the channels the event should broadcast on
	 */
	public function broadcastOn() {
		$channelType = config('finder.broadcasting.channel_type', 'private');
		$channelName = config('finder.broadcasting.channel_name', 'finder');

        if( $channelType === 'public' || ! Auth::user() ){
            return [
				new Channel($channelName)
			];
        }

		return [
			new PrivateChannel("{$channelName}." . auth()->user()->id)
		];
	}
}