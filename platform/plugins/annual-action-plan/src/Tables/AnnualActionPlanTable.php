<?php

namespace Impiger\AnnualActionPlan\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\AnnualActionPlan\Repositories\Interfaces\AnnualActionPlanInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\AnnualActionPlan\Models\AnnualActionPlan;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class AnnualActionPlanTable extends TableAbstract
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
     * @var string
     */
    protected $view = "core/table::table";
    protected $editPermissions = "annual-action-plan.edit";
    protected $deletePermissions = "annual-action-plan.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * AnnualActionPlanTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AnnualActionPlanInterface $annualActionPlanRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AnnualActionPlanInterface $annualActionPlanRepository)
    {
        $this->repository = $annualActionPlanRepository;
        $this->setOption('id', 'plugins-annual-action-plan-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'annual-action-plan', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('financial_year_id', function($item) { 
				return CrudHelper::formatRows($item->financial_year_id, 'database', 'financial_year|id|session_year', $item, '');
			})
			->editColumn('division_id', function($item) { 
				return CrudHelper::formatRows($item->division_id, 'database', 'divisions|id|name', $item, '');
			})
			->editColumn('officer_incharge_designation_id', function($item) { 
				return CrudHelper::formatRows($item->officer_incharge_designation_id, 'database', 'officer_incharge_designations|id|name', $item, '');
			})
			->editColumn('training_module', function($item) { 
				return CrudHelper::formatRows($item->training_module, 'radio', '1:Online,0:OffLine', $item, '');
			})
			->editColumn('submit_date', function($item) { 
				return CrudHelper::formatRows($item->submit_date, 'date', '', $item, '');
			})
			->editColumn('created_at', function($item) { 
				return CrudHelper::formatDateTime($item->created_at);
			})
			->editColumn('updated_at', function($item) { 
				return CrudHelper::formatDateTime($item->updated_at);
			})
			->editColumn('modified_by', function($item) { 
				return CrudHelper::formatDate($item->modified_by);
			})
			->editColumn('deleted_at', function($item) { 
				return CrudHelper::formatDateTime($item->deleted_at);
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='annual-action-plans/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'annual_action_plan.*'
        ];

        $query = $model->select($select)->whereNotNull('annual_action_plan.id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'name' => [
			'name' => 'name',
			'title' => 'Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'financial_year_id' => [
			'name' => 'financial_year_id',
			'title' => 'Financial Year',
			'width' => '100',
			'class' => 'text-left'
			],
			'division_id' => [
			'name' => 'division_id',
			'title' => 'Division',
			'width' => '100',
			'class' => 'text-left'
			],
			'officer_incharge_designation_id' => [
			'name' => 'officer_incharge_designation_id',
			'title' => 'Officer Incharge Designation',
			'width' => '100',
			'class' => 'text-left'
			],
			'duration' => [
			'name' => 'duration',
			'title' => 'Duration',
			'width' => '100',
			'class' => 'text-left'
			],
			'no_of_batches' => [
			'name' => 'no_of_batches',
			'title' => 'No Of Batches',
			'width' => '100',
			'class' => 'text-left'
			],
			'batch_size' => [
			'name' => 'batch_size',
			'title' => 'Batch Size',
			'width' => '100',
			'class' => 'text-left'
			],
			'budget_per_program' => [
			'name' => 'budget_per_program',
			'title' => 'Budget Per Program',
			'width' => '100',
			'class' => 'text-left'
			],
			'total_budget' => [
			'name' => 'total_budget',
			'title' => 'Total Budget',
			'width' => '100',
			'class' => 'text-left'
			],
			'training_module' => [
			'name' => 'training_module',
			'title' => 'Training Type',
			'width' => '100',
			'class' => 'text-left'
			],
			'remarks' => [
			'name' => 'remarks',
			'title' => 'Remarks',
			'width' => '100',
			'class' => 'text-left'
			],
			'submit_date' => [
			'name' => 'submit_date',
			'title' => 'Submit Date',
			'width' => '100',
			'class' => 'text-left'
			],
			'created_at' => [
			'name' => 'created_at',
			'title' => 'Created At',
			'width' => '100',
			'class' => 'text-left'
			],
			'created_by' => [
			'name' => 'created_by',
			'title' => 'Created By',
			'width' => '100',
			'class' => 'text-left'
			],
			'updated_at' => [
			'name' => 'updated_at',
			'title' => 'Updated At',
			'width' => '100',
			'class' => 'text-left'
			],
			'modified_by' => [
			'name' => 'modified_by',
			'title' => 'Modified By',
			'width' => '100',
			'class' => 'text-left'
			],
			'deleted_at' => [
			'name' => 'deleted_at',
			'title' => 'Deleted At',
			'width' => '100',
			'class' => 'text-left'
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

        $buttons = $this->addCreateButton(route('annual-action-plan.create'), 'annual-action-plan.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, AnnualActionPlan::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('annual-action-plan.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('annual-action-plan.deletes'), 'annual-action-plan.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('annual-action-plan', $isFilter, );
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges(true);
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

        if (Auth::user() && Auth::user()->hasPermission('annual-action-plan.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('annual-action-plan.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'annual-action-plan.edit';
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
