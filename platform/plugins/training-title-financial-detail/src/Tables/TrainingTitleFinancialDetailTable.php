<?php

namespace Impiger\TrainingTitleFinancialDetail\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\TrainingTitleFinancialDetail\Repositories\Interfaces\TrainingTitleFinancialDetailInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class TrainingTitleFinancialDetailTable extends TableAbstract
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
    protected $editPermissions = "training-title-financial-detail.edit";
    protected $deletePermissions = "training-title-financial-detail.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * TrainingTitleFinancialDetailTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param TrainingTitleFinancialDetailInterface $trainingTitleFinancialDetailRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, TrainingTitleFinancialDetailInterface $trainingTitleFinancialDetailRepository)
    {
        $this->repository = $trainingTitleFinancialDetailRepository;
        $this->setOption('id', 'plugins-training-title-financial-detail-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'training-title-financial-detail', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('division_id', function($item) { 
				return CrudHelper::formatRows($item->division_id, 'database', 'divisions|id|name', $item, '');
			})
			->editColumn('financial_year_id', function($item) { 
				return CrudHelper::formatRows($item->financial_year_id, 'database', 'financial_year|id|session_year', $item, '');
			})
			->editColumn('annual_action_plan_id', function($item) { 
				return CrudHelper::formatRows($item->annual_action_plan_id, 'database', 'annual_action_plan|id|name', $item, '');
			})
			->editColumn('training_title_id', function($item) { 
				return CrudHelper::formatRows($item->training_title_id, 'database', 'training_title|id|code', $item, '');
			})
			->editColumn('neft_cheque_date', function($item) { 
				return CrudHelper::formatDate($item->neft_cheque_date);
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
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='training-title-financial-details/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'training_title_financial_details.id',
			'training_title_financial_details.division_id',
			'training_title_financial_details.financial_year_id',
			'training_title_financial_details.annual_action_plan_id',
			'training_title_financial_details.training_title_id',
			'training_title_financial_details.budget_approved',
			'training_title_financial_details.actual_expenditure',
			'training_title_financial_details.edi_admin_cost',
			'training_title_financial_details.revenue_generated',
			'training_title_financial_details.neft_cheque_no',
			'training_title_financial_details.neft_cheque_date',
			'training_title_financial_details.remarks',
			'training_title_financial_details.submit_date',
			'training_title_financial_details.created_by',
			'training_title_financial_details.created_at',
			'training_title_financial_details.updated_by',
			'training_title_financial_details.updated_at'
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
            'division_id' => [
			'name' => 'division_id',
			'title' => 'Division',
			'width' => '100',
			'class' => 'text-left'
			],
			'financial_year_id' => [
			'name' => 'financial_year_id',
			'title' => 'Financial Year',
			'width' => '100',
			'class' => 'text-left'
			],
			'annual_action_plan_id' => [
			'name' => 'annual_action_plan_id',
			'title' => 'Training/Workshop/Program Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'training_title_id' => [
			'name' => 'training_title_id',
			'title' => 'Training Name & Code',
			'width' => '100',
			'class' => 'text-left'
			],
			'budget_approved' => [
			'name' => 'budget_approved',
			'title' => 'Budget Approved',
			'width' => '100',
			'class' => 'text-left'
			],
			'actual_expenditure' => [
			'name' => 'actual_expenditure',
			'title' => 'Actual Expenditure',
			'width' => '100',
			'class' => 'text-left'
			],
			'edi_admin_cost' => [
			'name' => 'edi_admin_cost',
			'title' => 'Edi Admin Cost',
			'width' => '100',
			'class' => 'text-left'
			],
			'revenue_generated' => [
			'name' => 'revenue_generated',
			'title' => 'Revenue Generated',
			'width' => '100',
			'class' => 'text-left'
			],
			'neft_cheque_no' => [
			'name' => 'neft_cheque_no',
			'title' => 'Neft Cheque No',
			'width' => '100',
			'class' => 'text-left'
			],
			'neft_cheque_date' => [
			'name' => 'neft_cheque_date',
			'title' => 'Neft Cheque Date',
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
			'created_by' => [
			'name' => 'created_by',
			'title' => 'Created By',
			'width' => '100',
			'class' => 'text-left'
			],
			'created_at' => [
			'name' => 'created_at',
			'title' => 'Created At',
			'width' => '100',
			'class' => 'text-left'
			],
			'updated_by' => [
			'name' => 'updated_by',
			'title' => 'Updated By',
			'width' => '100',
			'class' => 'text-left'
			],
			'updated_at' => [
			'name' => 'updated_at',
			'title' => 'Updated At',
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

        $buttons = $this->addCreateButton(route('training-title-financial-detail.create'), 'training-title-financial-detail.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, TrainingTitleFinancialDetail::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('training-title-financial-detail.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('training-title-financial-detail.deletes'), 'training-title-financial-detail.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('training-title-financial-detail', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('training-title-financial-detail.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('training-title-financial-detail.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'training-title-financial-detail.edit';
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
