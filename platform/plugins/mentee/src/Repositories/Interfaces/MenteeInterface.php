<?php

namespace Impiger\Mentee\Repositories\Interfaces;

use Impiger\Support\Repositories\Interfaces\RepositoryInterface;

interface MenteeInterface extends RepositoryInterface
{
    public function getMenteesCountByRegionWise();
}
