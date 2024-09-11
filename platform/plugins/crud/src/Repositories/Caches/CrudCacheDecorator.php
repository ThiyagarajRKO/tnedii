<?php

namespace Impiger\Crud\Repositories\Caches;

use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;
use Impiger\Crud\Repositories\Interfaces\CrudInterface;

class CrudCacheDecorator extends CacheAbstractDecorator implements CrudInterface
{
    /**
     * {@inheritDoc}
     */
    public function getTrainingTitleLists($limit)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRecentTrainingTitleLists()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
