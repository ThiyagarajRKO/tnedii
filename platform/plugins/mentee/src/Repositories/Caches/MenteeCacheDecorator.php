<?php

namespace Impiger\Mentee\Repositories\Caches;

use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;
use Impiger\Mentee\Repositories\Interfaces\MenteeInterface;

class MenteeCacheDecorator extends CacheAbstractDecorator implements MenteeInterface
{
    public function getMenteesCountByRegionWise()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
