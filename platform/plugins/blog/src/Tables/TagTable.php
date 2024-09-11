<?php

namespace Impiger\Blog\Tables;

use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Blog\Repositories\Interfaces\TagInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TagTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;
    /*@Customized by Sabari Shankar Parthiban start*/
    protected $printPreview = 'base.print';
    /*@Customized by Sabari Shankar Parthiban end*/
    /**
     * TagTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param TagInterface $tagRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, TagInterface $tagRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $tagRepository;

        if (!Auth::user()->hasAnyPermission(['tags.edit', 'tags.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('tags.edit')) {
                    return $item->name;
                }

                return Html::link(route('tags.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                if ($this->request()->input('action') === 'excel') {
                    return $item->status->getValue();
                }
                return $item->status->toHtml();
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations('tags.edit', 'tags.destroy', $item);
            });

        return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'tags.id',
            'tags.name',
            'tags.created_at',
            'tags.status',
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'         => [
                'name'  => 'tags.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name'       => [
                'name'  => 'tags.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'status'     => [
                'name'  => 'tags.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
            'created_at' => [
                'name'  => 'tags.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return $this->addCreateButton(route('tags.create'), 'tags.create');
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('tags.deletes'), 'tags.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*@customized by sabari shankar parthiban
             'tags.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],*/
            'tags.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            /*@customized by sabari shankar parthiban
            'tags.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],*/
        ];
    }
    /**
     * @Customized by Sabari Shankar Parthiban start
     * {@inheritDoc}
     */
    public function getDefaultButtons(): array
    {
       /*
        return [
            'export',
            'reload',
        ];*/
        $defaultBtns = parent::getDefaultButtons();
        $defaultBtns = array_merge($defaultBtns,['export','print']);
        return $defaultBtns;
    }
    /*@Customized by Sabari Shankar Parthiban end*/
}
