<?php

namespace Impiger\AuditLog\Repositories\Interfaces;

use Impiger\Support\Repositories\Interfaces\RepositoryInterface;

interface AuditLogInterface extends RepositoryInterface
{
    /**
     * @param array $select
     * @return mixed
     */
    public function getUnread($select = ['*']);

    /**
     * @return int
     */
    public function countUnread();
}
