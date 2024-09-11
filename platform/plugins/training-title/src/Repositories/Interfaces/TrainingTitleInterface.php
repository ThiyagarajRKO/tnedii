<?php

namespace Impiger\TrainingTitle\Repositories\Interfaces;

use Impiger\Support\Repositories\Interfaces\RepositoryInterface;

interface TrainingTitleInterface extends RepositoryInterface
{
    public function getTrainingTitleListGalleryView($limit);
}
