<?php

namespace Impiger\Blog\Repositories\Eloquent;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;
use Impiger\Blog\Repositories\Interfaces\TagInterface;

class TagRepository extends RepositoriesAbstract implements TagInterface
{

    /**
     * {@inheritDoc}
     */
    public function getDataSiteMap()
    {
        $data = $this->model
            ->with('slugable')
            ->where('tags.status', BaseStatusEnum::PUBLISHED)
            ->select('tags.*')
            ->orderBy('tags.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getPopularTags($limit, array $with = ['slugable'], array $withCount = ['posts'])
    {
        $data = $this->model
            ->with($with)
            ->withCount($withCount)
            // customized by haritha murugavel start for MB-8946
            ->orderBy('tags.created_at', 'desc')
            // ->orderBy('posts_count', 'DESC')
            // customized by haritha murugavel end for MB-8946

            ->limit($limit)
            ;

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getAllTags($active = true)
    {
        $data = $this->model->select('tags.*');
        if ($active) {
            $data = $data->where('tags.status', BaseStatusEnum::PUBLISHED);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }
}
