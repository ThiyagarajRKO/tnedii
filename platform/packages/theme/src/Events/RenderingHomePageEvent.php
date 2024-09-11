<?php

namespace Impiger\Theme\Events;

use Impiger\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class RenderingHomePageEvent extends Event
{
    use SerializesModels;
}