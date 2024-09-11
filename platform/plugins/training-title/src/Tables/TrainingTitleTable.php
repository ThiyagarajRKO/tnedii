<?php

namespace Impiger\TrainingTitle\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\TrainingTitle\Models\TrainingTitle;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class TrainingTitleTable extends TableAbstract
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
    protected $editPermissions = "training-title.edit";
    protected $deletePermissions = "training-title.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * TrainingTitleTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param TrainingTitleInterface $trainingTitleRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, TrainingTitleInterface $trainingTitleRepository)
    {
        $this->repository = $trainingTitleRepository;
        $this->setOption('id', 'plugins-training-title-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'training-title', $isEdit, $isPublic);
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
			->editColumn('vendor_id', function($item) { 
				return CrudHelper::formatRows($item->vendor_id, 'database', 'vendors|id|name', $item, '');
			})
			->editColumn('officer_incharge_designation_id', function($item) { 
				return CrudHelper::formatRows($item->officer_incharge_designation_id, 'database', 'officer_incharge_designations|id|name', $item, '');
			})
			->editColumn('fee_paid', function($item) { 
				return CrudHelper::formatRows($item->fee_paid, 'radio', '1:Free,2:Paid', $item, '');
			})
			->editColumn('private_workshop', function($item) { 
				return CrudHelper::formatRows($item->private_workshop, 'radio', '0:No,1:Yes', $item, '');
			})
			->editColumn('training_start_date', function($item) { 
				return CrudHelper::formatRows($item->training_start_date, 'date', '', $item, '');
			})
			->editColumn('training_end_date', function($item) { 
				return CrudHelper::formatRows($item->training_end_date, 'date', '', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='training-titles/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".apply_filters(ADD_CUSTOM_ACTION,'',$this->repository->getModel(),$item));
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
            'training_title.*'
        ];

        $query = $model->select($select)->whereNotNull('training_title.id');

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
			'division_id' => [
			'name' => 'division_id',
			'title' => 'Division',
			'width' => '100',
			'class' => 'text-left'
			],
			'financial_year_id' => [
			'name' => 'financial_year_id',
			'title' => 'Year',
			'width' => '100',
			'class' => 'text-left'
			],
			'annual_action_plan_id' => [
			'name' => 'annual_action_plan_id',
			'title' => 'Annual Action Plan',
			'width' => '100',
			'class' => 'text-left'
			],
			'code' => [
			'name' => 'code',
			'title' => 'Code',
			'width' => '100',
			'class' => 'text-left'
			],
			'venue' => [
			'name' => 'venue',
			'title' => 'Venue',
			'width' => '100',
			'class' => 'text-left'
			],
			'email' => [
			'name' => 'email',
			'title' => 'Email',
			'width' => '100',
			'class' => 'text-left'
			],
			'phone' => [
			'name' => 'phone',
			'title' => 'Phone',
			'width' => '100',
			'class' => 'text-left'
			],
			'vendor_id' => [
			'name' => 'vendor_id',
			'title' => 'Vendor',
			'width' => '100',
			'class' => 'text-left'
			],
			'officer_incharge_designation_id' => [
			'name' => 'officer_incharge_designation_id',
			'title' => 'Officer Incharge Designation',
			'width' => '100',
			'class' => 'text-left'
			],
			'fee_paid' => [
			'name' => 'fee_paid',
			'title' => 'Fee Paid',
			'width' => '100',
			'class' => 'text-left'
			],
			'private_workshop' => [
			'name' => 'private_workshop',
			'title' => 'Private Workshop',
			'width' => '100',
			'class' => 'text-left'
			],
			'fee_amount' => [
			'name' => 'fee_amount',
			'title' => 'Fee Amount',
			'width' => '100',
			'class' => 'text-left'
			],
			'training_start_date' => [
			'name' => 'training_start_date',
			'title' => 'Training Start Date',
			'width' => '100',
			'class' => 'text-left'
			],
			'training_end_date' => [
			'name' => 'training_end_date',
			'title' => 'Training End Date',
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

        $buttons = $this->addCreateButton(route('training-title.create'), 'training-title.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, TrainingTitle::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('training-title.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('training-title.deletes'), 'training-title.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('training-title', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('training-title.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('training-title.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'training-title.edit';
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
