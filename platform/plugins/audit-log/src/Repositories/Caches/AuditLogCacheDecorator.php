<?php

namespace Impiger\AuditLog\Repositories\Caches;

use Impiger\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;

/**
 * @since 16/09/2016 10:55 AM
 */
class AuditLogCacheDecorator extends CacheAbstractDecorator implements AuditLogInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUnread($select = ['*'])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function countUnread()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
