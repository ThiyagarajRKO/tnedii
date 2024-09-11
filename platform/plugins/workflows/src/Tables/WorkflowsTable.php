<?php

namespace Impiger\Workflows\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Workflows\Repositories\Interfaces\WorkflowsInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Workflows\Models\Workflows;
use App\Utils\CrudHelper;
use Html;

class WorkflowsTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    protected $printPreview = 'base.print';
    /**
     * WorkflowsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param WorkflowsInterface $workflowsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, WorkflowsInterface $workflowsRepository)
    {
        $this->repository = $workflowsRepository;
        $this->setOption('id', 'plugins-workflows-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['workflows.edit', 'workflows.destroy'])) {
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
                if (!Auth::user()->hasPermission('workflows.edit')) {
                    return $item->name;
                }
                return Html::link(route('workflows.edit', $item->id), $item->name);
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
                $moreBtn ="";
                if(Auth::user()->hasPermission('workflows.map_permission')){
                    $moreBtn = "<a  href='workflows/update_permission/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='Map Permission'><i class='fa fa-share'></i></a>";
                }                
                $moreBtn .= CrudHelper::getRowActivationActionBtn($item,'Impiger\Workflows\Models\Workflows', 'workflows.enable_disable','', null, '/admin/workflows/row_activation/');
                return $this->getOperations('workflows.edit', 'workflows.destroy', $item, $moreBtn);
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
            'workflows.id',
            'workflows.name',
            'workflows.created_at',
            'workflows.status',
            'module_controller',
            'module_property',
            'is_enabled'
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
            'id' => [
                'name'  => 'workflows.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'visible' => false
            ],
            'name' => [
                'name'  => 'workflows.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'module_controller' => [
                'name'  => 'workflows.module_controller',
                'title' => 'Module Table',
                'class' => 'text-left',
            ],
            'module_property' => [
                'name'  => 'workflows.module_property',
                'title' => 'Module Property',
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
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = [];
        $buttons = $this->addCreateButton(route('workflows.create'), 'workflows.create');
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Workflows::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('workflows.deletes'), 'workflows.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return ['workflows.name'   => [
                'title'    => 'Name',
                'type'     => 'text',
            ],];
    }
    /**
     * @return array
     */
    public function getDefaultButtons(): array
    {
        $defaultBtns = parent::getDefaultButtons();

        if (!$this->hasActions) {
            return $defaultBtns;
        }

        if (Auth::user() && Auth::user()->hasPermission('workflows.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('workflows.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }
}
