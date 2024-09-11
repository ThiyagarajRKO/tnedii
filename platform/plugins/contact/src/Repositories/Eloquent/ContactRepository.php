<?php

namespace Impiger\Contact\Repositories\Eloquent;

use Impiger\Contact\Enums\ContactStatusEnum;
use Impiger\Contact\Repositories\Interfaces\ContactInterface;
use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;

class ContactRepository extends RepositoriesAbstract implements ContactInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUnread($select = ['*'])
    {
        $data = $this->model->where('status', ContactStatusEnum::UNREAD)->select($select);
        /* @Customized by Sabari Shankar Parthiban Start*/   
        if (is_plugin_active('multidomain')) {
            $data = apply_filters(BASE_FILTER_TABLE_QUERY, $data, $this->model, $select);
        }
        $data = $data->get();
        /* @Customized by Sabari Shankar Parthiban End*/ 
        $this->resetModel();
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function countUnread()
    {
        $data = $this->model->where('status', ContactStatusEnum::UNREAD);
        /* @Customized by Sabari Shankar Parthiban Start*/   
        if (is_plugin_active('multidomain')) {
            $data = apply_filters(BASE_FILTER_TABLE_QUERY, $data, $this->model, $select);
        }
        $data = $data->count();
        /* @Customized by Sabari Shankar Parthiban End*/ 
        $this->resetModel();
        return $data;
    }
}
