<?php

namespace Impiger\Entrepreneur\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Entrepreneur\Models\Trainee;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class TraineeTable extends TableAbstract
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
    protected $view = "plugins/crud::import.list";
    protected $editPermissions = "trainee.edit";
    protected $deletePermissions = "trainee.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * TraineeTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param TraineeInterface $traineeRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, TraineeInterface $traineeRepository)
    {
        $this->repository = $traineeRepository;
        $this->setOption('id', 'plugins-trainee-table');
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
                return CrudHelper::getNameFieldLink($item, 'trainee', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('entrepreneur_id', function($item) { 
				return CrudHelper::formatRows($item->entrepreneur_id, 'database', 'entrepreneurs|id|name', $item, '');
			})
            ->filterColumn('entrepreneur_id', function($query, $keyword) {
                $sql = 'E.name like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
                $sql = 'E.email like ?';
                $query->orWhereRaw($sql, ["%{$keyword}%"]);
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
				return CrudHelper::formatRows($item->training_title_id, 'database', 'training_title|id|name', $item, '');
			})
			->editColumn('training_title_fee_status', function($item) { 
				return CrudHelper::formatRows($item->training_title_id, 'database', 'training_title|id|fee_paid', $item, '');
			})
			->editColumn('certificate_status', function($item) { 
				return CrudHelper::formatRows($item->certificate_status, 'radio', '1:Available,0:Not Available', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('certificate_generated_at', function ($item) {
                return BaseHelper::formatDate($item->certificate_generated_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                #{hideOnEditAction}
                #{hideOnDeleteAction}
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='trainees/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".apply_filters(ADD_CUSTOM_ACTION,'',$this->repository->getModel(),$item));
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
            'trainees.id',
			'trainees.entrepreneur_id',
			'trainees.division_id',
			'trainees.financial_year_id',
			'trainees.annual_action_plan_id',
			'trainees.training_title_id',
			'trainees.certificate_status',
			'trainees.certificate_generated_at',
			'trainees.file_path',
			'trainees.created_at',
			'trainees.updated_at',
			'trainees.deleted_at'
        ];

        $query = $model->select($select)
        ->leftJoin('entrepreneurs AS E','E.id','=','trainees.entrepreneur_id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'entrepreneur_id' => [
			'name' => 'entrepreneur_id',
			'title' => 'Entrepreneur Name',
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
			'certificate_status' => [
			'name' => 'certificate_status',
			'title' => 'Certificate Status',
			'width' => '100',
			'class' => 'text-left'
            ],
			'certificate_generated_at' => [
			'name' => 'certificate_generated_at',
			'title' => 'Certificate Generated',
			'width' => '100',
			'class' => 'text-left'
			],
			'training_title_fee_status' => [
			'name' => 'training_title_id',
			'title' => 'Fee Type',
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

        $buttons = $this->addCreateButton(route('trainee.create'), 'trainee.create');
        $buttons['bulk-upload'] = ['link' => '#','text' => view('plugins/crud::import.import')->render()];
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Trainee::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('trainee.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('trainee.deletes'), 'trainee.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('trainee', $isFilter);
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

        if (Auth::user() && Auth::user()->hasPermission('trainee.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('trainee.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'trainee.edit';
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
