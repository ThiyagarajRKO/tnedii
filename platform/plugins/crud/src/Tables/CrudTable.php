<?php

namespace Impiger\Crud\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Crud\Repositories\Interfaces\CrudInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use App\Models\Crud;
use Html;

class CrudTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * CrudTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param CrudInterface $crudRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CrudInterface $crudRepository)
    {
        $this->repository = $crudRepository;
        $this->setOption('id', 'plugins-crud-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['crud.edit', 'crud.destroy'])) {
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
                if (!Auth::user()->hasPermission('crud.edit')) {
                    return $item->name;
                }
                return Html::link(route('crud.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('crud.edit', 'crud.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'cruds.id',
            'cruds.module_name',
            'cruds.module_type',
            'cruds.module_title',
            'cruds.module_db',
            'cruds.module_db_key',
            'cruds.created_at',
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
            'module_name' => [
                'name'  => 'cruds.module_name',
                'title' => trans('core/base::tables.module_name'),
                'class' => 'text-left',
            ],
            'module_type' => [
                'name'  => 'cruds.module_type',
                'title' => trans('core/base::tables.module_type'),
                'class' => 'text-left',
            ],
            'module_title' => [
                'name'  => 'cruds.module_title',
                'title' => trans('core/base::tables.module_title'),
                'class' => 'text-left',
            ],
            'module_db' => [
                'name'  => 'cruds.module_db',
                'title' => trans('core/base::tables.module_db'),
                'class' => 'text-left',
            ],
            'module_db_key' => [
                'name'  => 'cruds.module_db_key',
                'title' => trans('core/base::tables.module_db_key'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'cruds.created_at',
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
        $buttons = $this->addCreateButton(route('crud.create'), 'crud.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Crud::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('crud.deletes'), 'crud.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'cruds.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'cruds.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'cruds.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }
}
