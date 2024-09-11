<?php

namespace Impiger\ACL\Repositories\Caches;

use Impiger\ACL\Repositories\Interfaces\RoleInterface;
use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;

class RoleCacheDecorator extends CacheAbstractDecorator implements RoleInterface
{
    /**
     * {@inheritDoc}
     */
    public function createSlug($name, $id)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }
}
