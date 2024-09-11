<?php

namespace Impiger\Crud\Repositories\Interfaces;

use Impiger\Support\Repositories\Interfaces\RepositoryInterface;

interface CrudInterface extends RepositoryInterface
{
    /**
     * @param int $limit
     * @param array $with
     */
    // public function getInstitutionLists($limit);
    public function getTrainingTitleLists($limit);
}
