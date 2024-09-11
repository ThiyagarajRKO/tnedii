<?php

namespace Impiger\Workflows\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Workflows\Repositories\Interfaces\WorkflowPermissionInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Workflows\Models\WorkflowPermission;
use Html;

class WorkflowPermissionTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = false;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    protected $printPreview = 'base.print';
    /**
     * WorkflowPermissionTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param WorkflowPermissionInterface $workflowPermissionRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, WorkflowPermissionInterface $workflowPermissionRepository)
    {
        $this->repository = $workflowPermissionRepository;
        $this->setOption('id', 'plugins-workflow-permission-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['workflow-permission.edit', 'workflow-permission.destroy'])) {
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
                if (!Auth::user()->hasPermission('workflow-permission.edit')) {
                    return $item->name;
                }
                return Html::link(route('workflow-permission.edit', $item->id), $item->name);
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
                return $this->getOperations('workflow-permission.edit', 'workflow-permission.destroy', $item);
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
            'workflow_permissions.id',
            'workflows.name',
            'workflows.created_at',
            'workflows.status',
        ];

        $query = $model
        ->groupBy(\DB::raw('workflows_id'))->select($select)->join('workflows', 'workflow_permissions.workflows_id', '=', 'workflows.id');
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id' => [
                'name'  => 'workflow_permissions.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name'  => 'workflows.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'workflows.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'workflows.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = [];

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, WorkflowPermission::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('workflow-permission.deletes'), 'workflow-permission.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'workflows.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'workflows.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'workflows.created_at' => [
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
