<?php

namespace Impiger\TrainingTitle\Repositories\Caches;

use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;

class TrainingTitleCacheDecorator extends CacheAbstractDecorator implements TrainingTitleInterface
{
    public function getTrainingTitleListGalleryView($limit)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
