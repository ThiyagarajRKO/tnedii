<?php

namespace Impiger\SpokeRegistration\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\SpokeRegistration\Repositories\Interfaces\SpokeEcellsInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\SpokeRegistration\Models\SpokeEcells;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;
use Impiger\Workflows\Traits\WorkflowProperty;


class SpokeEcellsTable extends TableAbstract
{
    use WorkflowProperty;
    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * @var string
     */
    protected $view = "core/table::table";
    protected $editPermissions = "spoke-ecells.edit";
    protected $deletePermissions = "spoke-ecells.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * SpokeEcellsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param SpokeEcellsInterface $spokeEcellsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, SpokeEcellsInterface $spokeEcellsRepository)
    {
        $this->repository = $spokeEcellsRepository;
        $this->setOption('id', 'plugins-spoke-ecells-table');
        parent::__construct($table, $urlGenerator);

        
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                $this->checkDefault($item);
                $isEdit =  (!empty($this->editPermissions));
                $isPublic =  $this->getOption('shortcode');

                #{hideOnEditLink}
                return CrudHelper::getNameFieldLink($item, 'spoke-ecells', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('spoke_registration_id', function($item) { 
				return CrudHelper::formatRows($item->spoke_registration_id, 'database', 'spoke_registration|id|name', $item, '');
			})
			->editColumn('wf_status', function($item) { 
				return CrudHelper::formatRows($item->wf_status, 'workflow', '', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                #{hideOnEditAction}
                #{hideOnDeleteAction}
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='spoke-ecells/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".CrudHelper::getRowActivationActionBtn($item,'Impiger\SpokeRegistration\Models\SpokeEcells', 'spoke-ecells.enable_disable',''));
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
            'spoke_ecells.id',
			'spoke_ecells.spoke_registration_id',
			'spoke_ecells.name',
			'spoke_ecells.wf_status'
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
            'spoke_registration_id' => [
			'name' => 'spoke_registration_id',
			'title' => 'Spoke Institution',
			'width' => '100',
			'class' => 'text-left'
			],
			'name' => [
			'name' => 'name',
			'title' => 'Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'wf_status' => [
			'name' => 'wf_status',
			'title' => 'Status',
			'width' => '100',
			'class' => 'text-left',
			'class' => 'text-left workflow-dropdown'
			]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        if (!$this->hasActions) {
            return [];
        }

        $buttons = $this->addCreateButton(route('spoke-ecells.create'), 'spoke-ecells.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, SpokeEcells::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('spoke-ecells.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('spoke-ecells.deletes'), 'spoke-ecells.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('spoke-ecells', $isFilter, );
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
         $filters = $this->getBulkChanges(true);
        $workflow = [];
        if (is_plugin_active('workflows') && $this->isWorkflowSupport('spoke_ecells')) {
            $workflowStates = \CustomWorkflow::getWorkflowAllStates('spoke_ecells');
            foreach ($workflowStates as $key => $value) {
                $workflow[$value] = ucfirst($value);
            }
        }
        $filters['spoke_ecells.wf_status'] = [
            'title' => 'Status',
            'type' => 'select',
            'choices' => $workflow
        ];
        return $filters;
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

        if (Auth::user() && Auth::user()->hasPermission('spoke-ecells.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('spoke-ecells.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'spoke-ecells.edit';
            }else{
                $this->editPermissions = '';
            }
            $this->deletePermissions = '';
        }
    }

    public function setTableConfig($config): self
    {
        $this->hasActions = (isset($config->hasActions)) ? $config->hasActions : false;
        $this->hasOperations = (isset($config->hasOperations)) ? $config->hasOperations : false;
        $this->hasCheckbox = (isset($config->hasCheckbox)) ? $config->hasCheckbox : false;
        $this->pageLength = (isset($config->pageLength)) ? $config->pageLength : $this->pageLength;
        return $this;
    }

    

}
