<?php

namespace Impiger\RequestLog\Repositories\Caches;

use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;
use Impiger\RequestLog\Repositories\Interfaces\RequestLogInterface;

class RequestLogCacheDecorator extends CacheAbstractDecorator implements RequestLogInterface
{
}
