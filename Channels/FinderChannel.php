<?php

namespace App\Channels;

use Illuminate\Foundation\Auth\User;

class FinderChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, int $id): bool
    {
		return $user->id === $id;
    }
}
