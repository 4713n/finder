<?php

use Illuminate\Support\Facades\Broadcast;
use App\Channels\FinderChannel;

Broadcast::channel(config('finder.broadcasting.channel_name') . '.{id}', FinderChannel::class);