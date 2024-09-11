<?php

namespace Impiger\Mentor\Repositories\Caches;

use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;
use Impiger\Mentor\Repositories\Interfaces\MentorInterface;

class MentorCacheDecorator extends CacheAbstractDecorator implements MentorInterface
{
    public function getMentorsCountByRegionWise()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
