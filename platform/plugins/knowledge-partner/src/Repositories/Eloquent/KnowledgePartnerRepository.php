<?php

namespace Impiger\KnowledgePartner\Repositories\Eloquent;

use Impiger\KnowledgePartner\Enums\KnowledgePartnerStatusEnum;
use Impiger\KnowledgePartner\Repositories\Interfaces\KnowledgePartnerInterface;
use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;

class KnowledgePartnerRepository extends RepositoriesAbstract implements KnowledgePartnerInterface
{
    /**
     * {@inheritDoc}
     */
    /*public function getUnread($select = ['*'])
    {
        $data = $this->model->where('status', KnowledgePartnerStatusEnum::UNREAD)->select($select);
        $data = $data->get();
        $this->resetModel();
        return $data;
    }*/

    /**
     * {@inheritDoc}
     */
    /*public function countUnread()
    {
        $data = $this->model->where('status', KnowledgePartnerStatusEnum::UNREAD);
        $data = $data->count();
        $this->resetModel();
        return $data;
    }*/
}
