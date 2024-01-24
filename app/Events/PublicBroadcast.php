<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;

/**
 * Class PublicBroadcast
 */
abstract class PublicBroadcast extends Broadcast
{
	public function broadcastOn(): Channel
	{
		return new Channel('public');
	}
}
