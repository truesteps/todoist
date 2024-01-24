<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class Broadcast
 */
abstract class Broadcast extends Event implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
}
