<?php

namespace Impiger\Widget\Repositories\Eloquent;

use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;
use Impiger\Widget\Repositories\Interfaces\WidgetInterface;

class WidgetRepository extends RepositoriesAbstract implements WidgetInterface
{
    /**
     * {@inheritDoc}
     */
    public function getByTheme($theme)
    {
        $data = $this->model->where('theme', $theme)->get();
        $this->resetModel();

        return $data;
    }
}
