<?php

namespace Impiger\Dashboard\Repositories\Caches;

use Impiger\Dashboard\Repositories\Interfaces\DashboardWidgetSettingInterface;
use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;

class DashboardWidgetSettingCacheDecorator extends CacheAbstractDecorator implements DashboardWidgetSettingInterface
{
    /**
     * {@inheritDoc}
     */
    public function getListWidget()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
