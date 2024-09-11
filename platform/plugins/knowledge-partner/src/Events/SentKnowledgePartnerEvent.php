<?php

namespace Impiger\KnowledgePartner\Events;

use Impiger\Base\Events\Event;
use Eloquent;
use Illuminate\Queue\SerializesModels;
use stdClass;

class SentKnowledgePartnerEvent extends Event
{
    use SerializesModels;

    /**
     * @var Eloquent|false
     */
    public $data;

    /**
     * SentKnowledgePartnerEvent constructor.
     * @param Eloquent|false|stdClass $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
}
