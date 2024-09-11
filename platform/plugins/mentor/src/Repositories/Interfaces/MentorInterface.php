<?php

namespace Impiger\Mentor\Repositories\Interfaces;

use Impiger\Support\Repositories\Interfaces\RepositoryInterface;

interface MentorInterface extends RepositoryInterface
{
    public function getMentorsCountByRegionWise();
}
