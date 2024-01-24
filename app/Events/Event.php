<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class Event
 */
abstract class Event
{
	use Dispatchable;
	use SerializesModels;
}
