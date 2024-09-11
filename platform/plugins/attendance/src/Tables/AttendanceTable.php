<?php

namespace Impiger\Attendance\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Attendance\Repositories\Interfaces\AttendanceInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Attendance\Models\Attendance;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;
// use Impiger\Crud\Http\Controllers\CrudController;

class AttendanceTable extends TableAbstract
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
    protected $filterTemplate = 'plugins/attendance::attendance-filter';
    protected $editPermissions = "";
    protected $deletePermissions = "attendance.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * AttendanceTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AttendanceInterface $attendanceRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AttendanceInterface $attendanceRepository)
    {
        $this->repository = $attendanceRepository;
        $this->setOption('id', 'plugins-attendance-table');
        parent::__construct($table, $urlGenerator);
        $this->hasOperations = false;
        if (!request()->has('filter_table_id')) {
            $this->builder()->parameters(['deferLoading' => 0]);
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
                $this->checkDefault($item);
                $isEdit =  (!empty($this->editPermissions));
                $isPublic =  $this->getOption('shortcode');

                
                return CrudHelper::getNameFieldLink($item, 'attendance', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            // ->filterColumn('financial_year_id', function($query, $keyword) {
            //     $sql = 'FY.session_year like ?';
			// 	$query->whereRaw($sql, ["%{$keyword}%"]);
            // })
            ->editColumn('financial_year_id', function($item) { 
				return CrudHelper::formatLookupValue($item->financial_year_id, '1:financial_year:id:session_year');
			})
            ->filterColumn('annual_action_plan_id', function($query, $keyword) {
                $sql = 'AP.name like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
			->editColumn('annual_action_plan_id', function($item) { 
				return CrudHelper::formatLookupValue($item->annual_action_plan_id, '1:annual_action_plan:id:name');
			})
			
            ->filterColumn('training_title_id', function($query, $keyword) {
                $sql = 'TT.name  like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
                $sql = 'TT.code like ?';
                $query->orWhereRaw($sql, ["%{$keyword}%"]);
            })
			->editColumn('training_title_id', function($item) { 
				return CrudHelper::formatLookupValue($item->training_title_id, '1:training_title:id:code|venue');
			})
			->editColumn('attendance_date', function($item) { 
				return CrudHelper::formatDate($item->attendance_date);
			})
			->editColumn('present', function ($item) {
                $options = ['key' => $item->id, 'type' => 'radio'];
                return CrudHelper::getCustomFields('present', $options, $this->repository->getModel());
            })
            ->editColumn('absent', function ($item) {
                $options = ['key' => $item->id, 'type' => 'radio'];
                return CrudHelper::getCustomFields('absent', $options, $this->repository->getModel());
            })
            ->filterColumn('entrepreneur_id', function($query, $keyword) {
                $sql = 'E.name  like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->editColumn('entrepreneur_id', function($item) {
                return CrudHelper::formatLookupValue($item->entrepreneur_id, '1:entrepreneurs:id:name');
            })            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('remark', function($item) {
                $options = ['key' => $item->entrepreneur_id, 'type' => 'textarea', 'value' => $item->remark];
                return CrudHelper::getCustomFields('remark['.$item->entrepreneur_id.']', $options, $this->repository->getModel());
            })
            ->addColumn('operations', function ($item) {
                // $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                // $this->checkDefault($item);
                
                
                // return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='attendances/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
                $editPermissions = $this->editPermissions;
                $deletePermissions = $this->deletePermissions;
                if ($this->checkDefault($item)) {
                    $editPermissions = "";
                    $deletePermissions = "";
                }


                return $this->getOperations($editPermissions, $deletePermissions, $item);
            });

            // $data-dd();

            return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'T.entrepreneur_id AS id',
            'attendance.id AS attendance_id',
            'attendance.financial_year_id',
            DB::raw('IFNULL(attendance.annual_action_plan_id,T.annual_action_plan_id) AS annual_action_plan_id'),
            DB::raw('IFNULL(attendance.training_title_id,T.training_title_id) AS training_title_id'),
            'E.id AS entrepreneur_id',
            'attendance.attendance_date',
            'attendance.present',
            'attendance.absent',
            'attendance.created_at',
            'attendance.updated_at',
            'attendance.deleted_at'
        ];

        // $query = $model->select($select)->rightJoin('trainees AS T','T.entrepreneur_id','=','attendance.entrepreneur_id')->join('entrepreneurs AS E','attendance.entrepreneur_id','=','E.id')->join('training_title AS TT','TT.id','=','T.training_title_id')->whereRaw('TT.training_start_date <= NOW()')->whereRaw('TT.training_end_date > NOW()')->groupBy('attendance.entrepreneur_id');
        $query = $model->select($select)->rightJoin('trainees AS T','T.entrepreneur_id','=','attendance.entrepreneur_id')
        ->join('entrepreneurs AS E','T.entrepreneur_id','=','E.id')
        ->join('annual_action_plan AS AP','AP.id','=','T.annual_action_plan_id')
        ->join('training_title AS TT','TT.id','=','T.training_title_id')->groupBy('T.entrepreneur_id');
        $query = $query->whereRaw('T.training_title_id IS NOT NULL');
		$query = $query->whereRaw('T.deleted_at IS NULL');
        // $query->dd();
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            // 'financial_year_id' => [
			// 'name' => 'financial_year_id',
			// 'title' => 'Financial Year',
			// 'width' => '100',
			// 'class' => 'text-left'
			// ],
            'entrepreneur_id' => [
                'name' => 'entrepreneur_id',
                'title' => 'Entrepreneur',
                'width' => '100',
                'class' => 'text-left'
            ],
			// 'annual_action_plan_id' => [
			// 'name' => 'annual_action_plan_id',
			// 'title' => 'Annual Action Plan Id',
			// 'width' => '100',
			// 'class' => 'text-left'
			// ],
			'annual_action_plan_id' => [
			'name' => 'annual_action_plan_id',
			'title' => 'Training/Workshop/Program Name',
			'width' => '100',
			'class' => 'text-left'
			],
			// 'training_title_id' => [
			// 'name' => 'training_title_id',
			// 'title' => 'Training Title Id',
			// 'width' => '100',
			// 'class' => 'text-left'
			// ],
			'training_title_id' => [
			'name' => 'training_title_id',
			'title' => 'Training Name & Code',
			'width' => '100',
			'class' => 'text-left'
			],
			// 'attendance_date' => [
			// 'name' => 'attendance_date',
			// 'title' => 'Attendance Date',
			// 'width' => '100',
			// 'class' => 'text-left'
			// ],
			'present' => [
                'name' => 'present',
                'title' => 'Present',
                'width' => '100',
                'class' => 'text-left',
                'title'      => "<label for='present-check-all' class='attendanceLbl'>Present </label>" . \Form::input('checkbox', null, null, [
                    'class'    => 'present-check-all',
                    'data-set' => 'radio-present',
                    'id' => 'present-check-all'
                ])->toHtml(),
                'orderable' => false
            ],
            'absent' => [
                'name' => 'absent',
                'title' => 'Absent',
                'width' => '100',
                'class' => 'text-left',
                'title'      => "<label for='absent-check-all' class='attendanceLbl'>Absent </label>" . \Form::input('checkbox', null, null, [
                    'class'    => 'absent-check-all',
                    'data-set' => 'radio-absent',
                    'id' => 'absent-check-all'
                ])->toHtml(),
                'orderable' => false
            ],

            'remark' => [
                'name' => 'remark',
                'title' => 'Remark',
                'width' => '100',
                'class' => 'text-left',
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

        $buttons = [];
        $buttons = CrudHelper::getInlineEditBtn($buttons, ['attendance.inline_edit'], $this);
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Attendance::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('attendance.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('attendance.deletes'), 'attendance.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('attendance', $isFilter);
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        // return $this->getBulkChanges(true);
        $filters = $this->getBulkChanges(true);
        $filters['attendance.present']['choices'] = array(1 => 'Yes', 0 => 'No');
        $filters['attendance.absent']['choices'] = array(1 => 'Yes', 0 => 'No');
        $filters['attendance.financial_year_id']['choices'] = \Impiger\FinancialYear\Models\FinancialYear::where(['is_running' => 1])->pluck('session_year', 'id')->toArray();
        $filters['attendance.financial_year_id']['required'] = TRUE;
        $filters['attendance.annual_action_plan_id']['required'] = TRUE;
        $filters['attendance.training_title_id']['required'] = TRUE;
        // dd($filters);
        return  $filters;
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

        // if (Auth::user() && Auth::user()->hasPermission('attendance.export')) {
        //     $defaultBtns = array_merge($defaultBtns, ['export']);
        // }

        // if (Auth::user() && Auth::user()->hasPermission('attendance.print')) {
        //     $defaultBtns = array_merge($defaultBtns, ['print']);
        // }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        // if( isset($item->is_default) && $item->is_default){
        //     if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
        //         $this->editPermissions = 'attendance.edit';
        //     }else{
        //         $this->editPermissions = '';
        //     }
        //     $this->deletePermissions = '';
        // }

        if (isset($item->is_default) && $item->is_default) {
            return true;
        }
        return false;
    }

    public function setTableConfig($config): self
    {
        $this->hasActions = (isset($config->hasActions)) ? $config->hasActions : false;
        $this->hasOperations = (isset($config->hasOperations)) ? $config->hasOperations : false;
        $this->hasCheckbox = (isset($config->hasCheckbox)) ? $config->hasCheckbox : false;
        $this->pageLength = (isset($config->pageLength)) ? $config->pageLength : $this->pageLength;
        return $this;
    }

     /**
     * @param Builder $query
     * @param string $key
     * @param string $operator
     * @param string $value
     * @return Builder
     */
    public function applyFilterCondition($query, string $key, string $operator, ?string $value)
    {        
        switch ($key) {
            case 'training_title_id':
                if ($value == "") {
                    break;
                }

                return $query->where("T.training_title_id", $operator, $value);
            case 'annual_action_plan_id':
                if ($value == "") {
                    break;
                }

                return $query->where("T.annual_action_plan_id", $operator,$value);
            case 'financial_year_id':
                if ($value == "") {
                    break;
                }
    
                return $query->where("T.financial_year_id", $operator,$value);
            
        }
        return CrudHelper::applyFilterCondition($this->repository, $query,  $key,  $operator, $value);
    }

}
