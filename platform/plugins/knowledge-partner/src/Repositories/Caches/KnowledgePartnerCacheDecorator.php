<?php

namespace Impiger\KnowledgePartner\Repositories\Caches;

use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;
use Impiger\KnowledgePartner\Repositories\Interfaces\KnowledgePartnerInterface;

class KnowledgePartnerCacheDecorator extends CacheAbstractDecorator implements KnowledgePartnerInterface
{
    /**
     * {@inheritDoc}
     */
    /*public function getUnread($select = ['*'])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }*/

    /**
     * {@inheritDoc}
     */
    /*public function countUnread()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }*/
}
