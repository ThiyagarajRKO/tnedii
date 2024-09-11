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
use Log;
// use Impiger\Crud\Http\Controllers\CrudController;


class ViewAttendanceTable extends TableAbstract
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
    protected $view = "plugins/attendance::attendance";

    /**
     * @var string
     */
    protected $filterTemplate = 'plugins/attendance::attendance-filter';
    protected $editPermissions = "attendance.edit";
    protected $deletePermissions = "attendance.destroy";
    protected $viewAttendance = true;
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'plugins/crud::print';
    protected $viewAttendanceRemark = 'attendance-remark.index';
    /**
     * AttendanceTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AttendanceInterface $attendanceRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AttendanceInterface $attendanceRepository)
    {
        $this->repository = $attendanceRepository;
        $this->setOption('id', 'plugins-view-attendance-table');
        $this->hasCheckbox = false;
        $this->hasOperations = false;
        $this->setResponsive = false;
        $this->setScroll = true;

        parent::__construct($table, $urlGenerator);
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
                $isEdit =  (!empty($this->editPermissions) && !$this->checkDefault($item));
                $isPublic =  $this->getOption('shortcode');

                return CrudHelper::getNameFieldLink($item, 'attendance', $isEdit, $isPublic);
            })
            // ->filterColumn('student_name', function ($query, $keyword) {
            //     $sql = 'CONCAT_WS(" ",S.first_name,S.second_name)  like ?';
            //     $query->whereRaw($sql, ["%{$keyword}%"]);
            // })
            // ->filterColumn('registration_number', function ($query, $keyword) {
            //     $sql = 'AI.registration_number  like ?';
            //     $query->whereRaw($sql, ["%{$keyword}%"]);
            // })
            ->editColumn('attendance_date', function ($item) {
                return CrudHelper::formatDate($item->attendance_date);
            })
            ->editColumn('present', function ($item) {
                $options = ['value' => $item->present, 'key' => $item->id, 'type' => 'radio'];
                return CrudHelper::getCustomFields('present', $options, $this->repository->getModel());
            })
            ->editColumn('absent', function ($item) {
                $options = ['value' => $item->absent, 'key' => $item->id, 'type' => 'radio'];
                return CrudHelper::getCustomFields('absent', $options, $this->repository->getModel());
            })
            ->filterColumn('entrepreneur_id', function($query, $keyword) {
                $sql = 'E.name  like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->editColumn('entrepreneur_id', function ($item) {
                return CrudHelper::formatRows($item->entrepreneur_id, 'database', 'entrepreneurs|id|name', $item, '');
            })
            ->editColumn('financial_year_id', function ($item) {
                return CrudHelper::formatRows($item->financial_year_id, 'database', 'financial_year|id|session_year', $item, '');
            })
            // ->filterColumn('financial_year_id', function($query, $keyword) {
            //     $sql = 'FY.session_year like ?';
			// 	$query->whereRaw($sql, ["%{$keyword}%"]);
            // })
            ->editColumn('annual_action_plan_id', function ($item) {
                return CrudHelper::formatRows($item->annual_action_plan_id, 'database', 'annual_action_plan|id|name', $item, '');
            })
            ->filterColumn('annual_action_plan_id', function ($query, $keyword) {
                $sql = 'AAP.name like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->editColumn('training_title_id', function ($item) {
                return CrudHelper::formatRows($item->training_title_id, 'database', 'training_title|id|name:code', $item, '');
            })
            ->filterColumn('training_title_id', function ($query, $keyword) {
                $sql = 'TT.name like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            
            ->editColumn('updated_at', function ($item) {
                return CrudHelper::formatDateTime($item->updated_at);
            })

            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            });
        
        $period = $this->getAttendanceFilterDateColumn();
        // $columns = $this->columns();

        

        
        
        foreach ($period as $p) {
            $date = $p->format('Y-m-d');

            // Log::info($date);
            // $sessionTime = $this->getSessionDetails($date);
            $sessionTime = array();

            // $columnData = [];
            // $column = $p->format('D d/m');
            // $columnData['name'] = $column;
            // $columnData['data'] = $column;
            // $columnData['title'] = $column;
            // $columnData['class'] = 'text-center';
            // $columnData['searchable'] = false;
            // $columnData['sorting'] = false;
            // $columns[$column] = [];
            // $columns[$column] = $columnData;
            // $data->addColumn('Test',$columnData);
            $dbField = $p->format('D d/m');
            // Log::info($dbField);
            $data->editColumn($dbField, function ($item) use ($date) {
                $attendanceData = \Impiger\Attendance\Models\Attendance::where(['attendance_date' => $date, 'entrepreneur_id' => $item->entrepreneur_id])->get()->first();
                // Log::info($attendanceData);
                $output = "-";
                if($attendanceData) {
                    $output = $this->getAttendanceResult($attendanceData->present);
                    // Log::info($output);
                } 
                return $output;
            });
            /*
            
            if($sessionTime && count($sessionTime) >= 1){
                foreach($sessionTime as $session){
                    $dbField = $date.'_'.$session->db_field;
                    $startTime = $session->start_time;
                    $endTime = $session->end_time;
                    $data->editColumn($dbField, function ($item) use ($startTime, $endTime, $date) {
                        $attendanceData = \Impiger\Attendance\Models\Attendance::where(['attendance_date' => $date, 'entrepreneur_id' => $item->entrepreneur_id])->get()->toArray();
                        $output = "-";
                        if (count($attendanceData) == 1) {
                            $output = $this->getAttendanceResult(Arr::get($attendanceData, '0.present'));
                        } elseif (count($attendanceData) > 1) {
                            $prevOutput = "";
                            foreach($attendanceData as $k => $row) {
                                $output = $this->getAttendanceResult($row['present'], $prevOutput);
                                $prevOutput = $output;
                            }
                        }
                        return $output;
                    });
                }
            }
            else{
                $dbField = $p->format('D d/m');
                Log::info($dbField);
                $data->editColumn($dbField, function ($item) {
                    Log::info($item);
                    $aDate = $item['att_date']->format('D d/m');
                    $output = "-";
                    $output = $this->getAttendanceResult($row['present'], $prevOutput);
                    return $output;
                });
            }

            */
            
        }
        // dd($columns);
        // $table = new AttendanceTable(\DataTables $table, UrlGenerator $urlGenerator, AttendanceInterface $attendanceRepository);
        // $table->renderTable();
        // $dt = Datatables::of($model);

        $data->addColumn('operations', function ($item) {
            // $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
            // $this->checkDefault($item);
            
            
            // return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='attendances/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
            // $editPermissions = $this->editPermissions;
            // $deletePermissions = $this->deletePermissions;
            // if ($this->checkDefault($item)) {
            //     $editPermissions = "";
            //     $deletePermissions = "";
            // }


            // return $this->getOperations($editPermissions, $deletePermissions, $item);

            $editPermissions = $this->editPermissions;
            $deletePermissions = $this->deletePermissions;
                // $this->checkDefault($item);
                #{hideOnEditAction}
                #{hideOnDeleteAction}
            return $this->getOperations($this->editPermissions, $this->deletePermissions, $item);
        })->editColumn('remark', function ($item) {
            // $isEdit =  (!empty($this->editPermissions) && !$this->checkDefault($item));
            // $isPublic =  $this->getOption('shortcode');
            \Log::info("editColumn -> remark");
            \Log::info(json_encode($item));
            $link = "";
            if($this->getRemarkByCandidate($item->entrepreneur_id, $item->training_title_id)) {
                $filterUrl = "/admin/attendance-remarks?filter_table_id=plugins-attendance-remark-table";
                $filterUrl .= "&filter_columns[]=attendance_remarks.entrepreneur_id&filter_operators[]==&filter_values[]=".$item->entrepreneur_id;
                $filterUrl .= "&filter_columns[]=attendance_remarks.training_title_id&filter_operators[]==&filter_values[]=".$item->training_title_id;
                $link = "<a  href='".$filterUrl."' target='_blank' class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View Remaks History'>Remaks</a>";
            }
            return $link;
        });
        
        return $this->toJson($data);
    }

    public function getRemarkByCandidate($entrepreneurId, $trainingTitleId) {
        $model = $this->repository->getModel();
        $makeCondition = [
            'entrepreneur_id' => $entrepreneurId,
            'training_title_id' => $trainingTitleId,
        ];
        $data = $model->where($makeCondition)->whereNotNull('remark')->get()->toArray();
        if($data) {
            return count($data);
        }
        return 0;
    }

    public function getAttendanceResult($isPresent, $prevOutput = "")
    {
        $output = "-";
        if ($isPresent) {
            $output = "P";
        } elseif (!$isPresent) {
            $output = "A";
        }

        return $output;
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        /*
        $select = [
            'E.id','attendance.id AS attendance_id', 
            DB::raw('CONCAT_WS(" - ",AP.name,TT.code) AS training_program'),
            'attendance.financial_year_id', 
            'attendance.attendance_date', 'attendance.present', 'attendance.absent',
            'attendance.created_at', 'attendance.updated_at', 'attendance.deleted_at'
        ];
        
        $query = $model->select($select)->rightJoin('trainees AS T', 'attendance.entrepreneur_id', '=', 'T.entrepreneur_id')->whereNull('T.deleted_at')
            ->join('entrepreneurs AS E', 'E.id', '=', 'T.entrepreneur_id');

        $query = $query->leftJoin('training_title AS TT', 'TT.id', '=', 'attendance.training_title_id')
            ->leftJoin('annual_action_plan AS AP', 'AP.id', '=', 'TT.annual_action_plan_id')
            ->whereRaw('TT.training_start_date <= NOW()')
            ->orderBy('attendance.attendance_date','asc')
            ->groupBy('E.id');
        */

        $select = [
            'attendance.id',
            'attendance.financial_year_id',
            DB::raw('IFNULL(attendance.annual_action_plan_id,T.annual_action_plan_id) AS aap_id'),
            DB::raw('IFNULL(attendance.training_title_id,T.training_title_id) AS training_title_id'),
            'E.id AS entrepreneur_id',
            'attendance.attendance_date',
            'attendance.present',
            'attendance.absent',
            'attendance.remark',
            'attendance.created_at',
            'attendance.updated_at',
            'attendance.deleted_at'
        ];
        /*
        // $query = $model->select($select)->rightJoin('trainees AS T','T.entrepreneur_id','=','attendance.entrepreneur_id')->join('entrepreneurs AS E','attendance.entrepreneur_id','=','E.id')->join('training_title AS TT','TT.id','=','T.training_title_id')->whereRaw('TT.training_start_date <= NOW()')->whereRaw('TT.training_end_date > NOW()')->groupBy('attendance.entrepreneur_id');
        $query = $model->select($select)
                        ->rightJoin('trainees AS T','T.entrepreneur_id','=','attendance.entrepreneur_id')
                        ->join('entrepreneurs AS E','E.id','=','T.entrepreneur_id')
                        ->join('training_title AS TT','TT.id','=','T.training_title_id')
                        */
                        // ->whereRaw('TT.training_start_date <= NOW()')
        $query = $model->select($select)
        ->leftJoin('trainees AS T','T.training_title_id','=','attendance.training_title_id')
        ->leftJoin('entrepreneurs AS E','E.id','=','attendance.entrepreneur_id')
        ->leftJoin('training_title AS TT','TT.id','=','attendance.training_title_id')
        ->leftJoin('annual_action_plan AS AAP','AAP.id','=','attendance.annual_action_plan_id')
        ->groupBy('attendance.entrepreneur_id');

        // $query-dd();
        // \Log::info($query->dd());
        $data = $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
        // $data->dd();
        return $data;
    }

    public function getAttendanceFilterDateColumn()
    {
        $startDate = ($this->request()->get('attendance_startdate')) ? $this->request()->get('attendance_startdate') : \Carbon\Carbon::now()->subDays(6);
        $endDate = ($this->request()->get('attendance_enddate')) ? $this->request()->get('attendance_enddate') : \Carbon\Carbon::now();
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        // dd($period);
        return $period;
        // if($this->request()->get('attendance_startdate') && $this->request()->get('attendance_enddate')){
        //     $startDate = $this->request()->get('attendance_startdate');
        //     $endDate = $this->request()->get('attendance_enddate');
        //     $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        //     return $period;
        // } else {
        //     return [];
        // }
        
    }

    public function checkLoggedInUser($userId){
        $condition = array(
            'i.id'=> $userId
        );
        $query = DB::table('role_users AS ru')->select('i.id');
        $query->leftJoin('impiger_users AS i', function ($join){
            $join->on('i.user_id', '=', 'ru.user_id');
        });
        $query->leftJoin('roles AS r', function ($join){
            $join->on('r.id', '=', 'ru.role_id');
        });
        $query->where($condition);
        // $query->whereIN('r.slug',ATTENDANCE_ROLE_SLUG);
        return $query->pluck('id')->first();
    }
    /*
    public function checkTimetable($userId){
        $requestFilters = getRequestFilters(true);
        $academicYearId = (Arr::get($requestFilters,'academic_year_id')) ? Arr::get($requestFilters,'academic_year_id'):'';
        $programTypeId = (Arr::get($requestFilters,'program_type_id')) ? Arr::get($requestFilters,'program_type_id'):'';
        $instituteId = (Arr::get($requestFilters,'institute_id')) ? Arr::get($requestFilters,'institute_id'):'';
        $trainingProgramId = (Arr::get($requestFilters,'training_program_id')) ? Arr::get($requestFilters,'training_program_id'):'';
        $intakeId = (Arr::get($requestFilters,'intake_id')) ? Arr::get($requestFilters,'intake_id'):'';
        $term = (Arr::get($requestFilters,'term')) ? Arr::get($requestFilters,'term'):'';
            
        $condition = array(
            'td.trainer_id'=> $userId,
            't.academic_year_id' => $academicYearId,
            't.program_type_id' => $programTypeId,
            't.institute_id'=> $instituteId,
            't.training_program_id'=> $trainingProgramId,
            't.intake_id'=> $intakeId,
            't.term'=> $term,
            't.timetable_status'=> TIMETABLE_APPROVED_STATE
        );
        $query = DB::table('timetable_details AS td')->select('td.trainer_id');
        $query->leftJoin('timetable AS t', function ($join){
            $join->on('t.id', '=', 'td.timetable_id');
        });
        $query->where($condition);
        return $query->pluck('td.trainer_id')->first();
    }

    public function getSessionDetails($date){
            $dates = array(1 => 'Monday',2 => 'Tuesday',3 => 'Wednesday',4 => 'Thursday',5 => 'Friday',6 => 'Saturday',7 => 'Sunday');
            $day = $dates[date("N", strtotime($date))];
            $query = DB::table('attribute_options')->select('id')->where('name','=',$day);
            $day = $query->pluck('id')->first();
            $requestFilters = getRequestFilters(true);
            $academicYearId = (Arr::get($requestFilters,'academic_year_id')) ? Arr::get($requestFilters,'academic_year_id'):'';
            $programTypeId = (Arr::get($requestFilters,'program_type_id')) ? Arr::get($requestFilters,'program_type_id'):'';
            $instituteId = (Arr::get($requestFilters,'institute_id')) ? Arr::get($requestFilters,'institute_id'):'';
            $trainingProgramId = (Arr::get($requestFilters,'training_program_id')) ? Arr::get($requestFilters,'training_program_id'):'';
            $intakeId = (Arr::get($requestFilters,'intake_id')) ? Arr::get($requestFilters,'intake_id'):'';
            $term = (Arr::get($requestFilters,'term')) ? Arr::get($requestFilters,'term'):'';
            $sessionTypeId = (Arr::get($requestFilters,'session_type_id')) ? Arr::get($requestFilters,'session_type_id'):null;
            $courseUnitId = (Arr::get($requestFilters,'course_unit_id')) ? Arr::get($requestFilters,'course_unit_id'):null;
            $trainerId = (Arr::get($requestFilters,'trainer_id')) ? Arr::get($requestFilters,'trainer_id'):null;
            $userId = $this->checkLoggedInUser(getImpId(IMP_USER_TABLE,\Auth::id()));
            if($userId && !$trainerId){
                $userId = $this->checkTimetable($userId);
                if($userId){
                    $trainerId = $userId;
                }
            }
            $departmentId = (Arr::get($requestFilters,'department_id')) ? Arr::get($requestFilters,'department_id'):null;

            $condition = array(
                'td.day' => $day,
                'td.deleted_at' => NULL,
                't.academic_year_id' => $academicYearId,
                't.program_type_id' => $programTypeId,
                't.institute_id'=> $instituteId,
                't.training_program_id'=> $trainingProgramId,
                't.intake_id'=> $intakeId,
                't.term'=> $term,
                't.timetable_status'=> TIMETABLE_APPROVED_STATE
            );
            $query = DB::table('timetable_details AS td')->select("td.start_time", "td.end_time", \DB::raw("concat(td.start_time,'-',td.end_time) as db_field"), \DB::raw("concat(c.course_unit_code,'-',a.name,'(',td.start_time,'-',td.end_time,')') as text"));
            $query->leftJoin('timetable AS t', function ($join){
                $join->on('t.id', '=', 'td.timetable_id');
            });
            $query->leftJoin('course_units AS c', function ($join){
                $join->on('c.id', '=', 'td.course_unit_id');
            });
            $query->leftJoin('attribute_options AS a', function ($join){
                $join->on('a.id', '=', 'td.type');
            });
            $query->where($condition);
            if($courseUnitId != null){
                $query->where('td.course_unit_id', '=', $courseUnitId);
            }
            if($sessionTypeId != null){
                $query->where('td.type', '=', $sessionTypeId);
            }
            if($departmentId != null){
                $query->where('t.department_id', '=', $departmentId);
            }
            if($trainerId != null){
                $query->where('td.trainer_id', '=', $trainerId);
            }
            $query->orderBy("td.start_time", "ASC");
            $query->groupBy("text");
            return $query->get()->toArray();
    }

    */
    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        $columns =  [
            'training_title_id' => [
                'name' => 'training_title_id',
                'title' => 'Training Name & Code',
                'width' => '100',
                'class' => 'text-left'
            ],
            'entrepreneur_id' => [
                'name' => 'entrepreneur_id',
                'title' => 'Entrepreneur',
                'width' => '100',
                'class' => 'text-left'
            ],
            
        ];

        $period = $this->getAttendanceFilterDateColumn();
        foreach ($period as $p) {
            $date = $p->format('Y-m-d');
            // $sessionTime = $this->getSessionDetails($date);
            $sessionTime = array();
            if($sessionTime && count($sessionTime) >=1 ){
                foreach($sessionTime as $session){
                    $column = $date.'_'.$session->db_field;
                    $columnData = [];
                    $columnData['name'] = $column;
                    $columnData['data'] = $column;
                    $columnData['title'] = ($this->request()->input('action') == 'csv' || $this->request()->input('action') == 'print') ? $p->format('D d/m').' ['.$session->text.']' : $session->text;
                    $columnData['class'] = 'text-left';
                    $columnData['searchable'] = false;
                    $columnData['sorting'] = false;
                    $columns[$column] = [];
                    $columns[$column] = $columnData;
                }
            }else{
                $columnData = [];
                $column = $p->format('D d/m');
                $columnData['name'] = $column;
                $columnData['data'] = $column;
                $columnData['title'] = $column;
                $columnData['class'] = 'text-center';
                $columnData['searchable'] = false;
                $columnData['sorting'] = false;
                $columns[$column] = [];
                $columns[$column] = $columnData;
            }
        }

        $columns['remark'] = [
            'name' => 'remark',
            'title' => 'Remark',
            'width' => '100',
            'class' => 'text-left'
        ];

        return $columns;
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
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Attendance::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return [];
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
        $filters = $this->getBulkChanges(true);
        $filters['attendance.financial_year_id']['required'] = TRUE;
        // $filters['attendance.financial_year_id']['choices'] = getAcademicYearFilterChoices();
        $filters['attendance.financial_year_id']['choices'] = \Impiger\FinancialYear\Models\FinancialYear::where(['is_running' => 1])->pluck('session_year', 'id')->toArray();
        $filters['attendance.annual_action_plan_id']['required'] = TRUE;
        $filters['attendance.training_title_id']['required'] = TRUE;
        
        $filters['attendance.present']['choices'] = array(1 => 'Yes', 0 => 'No');
        $filters['attendance.absent']['choices'] = array(1 => 'Yes', 0 => 'No');
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

        if (Auth::user() && Auth::user()->hasPermission('attendance.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('attendance.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    /*

    protected function checkDefault($item)
    {
        if (isset($item->is_default) && $item->is_default) {
            return true;
        }
        return false;
    }

    */

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'attendance-remark.edit';
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

                return $query->where("attendance.training_title_id", $operator, $value);
            case 'annual_action_plan_id':
                if ($value == "") {
                    break;
                }

                return $query->where("attendance.annual_action_plan_id", $operator,$value);
            case 'financial_year_id':
                if ($value == "") {
                    break;
                }
    
                return $query->where("attendance.financial_year_id", $operator,$value);
            
        }
        return CrudHelper::applyFilterCondition($this->repository, $query,  $key,  $operator, $value);
    }
}
