<?php

namespace Impiger\Attendance\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Attendance\Http\Requests\AttendanceRequest;
use Impiger\Attendance\Repositories\Interfaces\AttendanceInterface;
use Impiger\Attendance\Repositories\Interfaces\AttendanceRemarkInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Attendance\Tables\AttendanceTable;
use Impiger\Attendance\Tables\ViewAttendanceTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Attendance\Forms\AttendanceForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
// use Impiger\Crud\Http\Controllers\CrudController;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use DB;

class AttendanceController extends BaseController
{
    /**
     * @var AttendanceInterface
     */
    protected $attendanceRepository;
    protected $attendanceRemarkRepository;

    /**
     * @param AttendanceInterface $attendanceRepository
     */
    public function __construct(AttendanceInterface $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
        $this->attendanceRemarkRepository = app(AttendanceRemarkInterface::class);

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js',
            'vendor/core/plugins/attendance/js/attendance.js',
            'vendor/core/plugins/attendance/js/attendance-training-program.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param AttendanceTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AttendanceTable $table)
    {
        page_title()->setTitle(trans('plugins/attendance::attendance.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/attendance::attendance.create'));

        return $formBuilder->create(AttendanceForm::class)->renderForm();
    }

    /**
     * @param AttendanceRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(AttendanceRequest $request, BaseHttpResponse $response )
    {
        
        
        $attendance = $this->attendanceRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(ATTENDANCE_MODULE_SCREEN_NAME, $request, $attendance));
        
        
        return $response
            ->setPreviousUrl(route('attendance.index'))
            ->setNextUrl(route('attendance.edit', $attendance->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        
        $attendance = $this->attendanceRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $attendance));

        $name = ($attendance->name) ? ' "' . $attendance->name . '"' : "";
        page_title()->setTitle(trans('plugins/attendance::attendance.edit') . $name);

        return $formBuilder->create(AttendanceForm::class, ['model' => $attendance])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $attendance = $this->attendanceRepository->findOrFail($id);
        $name = ($attendance->name) ? ' "' . $attendance->name . '"' : "";
        page_title()->setTitle(trans('plugins/attendance::attendance.view') . $name);

        return $formBuilder->create(AttendanceForm::class, ['model' => $attendance, 'isView' => true])->renderForm();
    }


     /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewAttendance(ViewAttendanceTable $table, Request $request)
    {
        page_title()->setTitle(trans('plugins/attendance::attendance.name'));
        return $table->renderTable();
    }
    

    /**
     * @param int $id
     * @param AttendanceRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, AttendanceRequest $request, BaseHttpResponse $response )
    {
        
        $attendance = $this->attendanceRepository->findOrFail($id);
        
        
        $attendance->fill($request->input());

        $this->attendanceRepository->createOrUpdate($attendance);

        event(new UpdatedContentEvent(ATTENDANCE_MODULE_SCREEN_NAME, $request, $attendance));
        
        
        return $response
            ->setPreviousUrl(route('attendance.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $attendance = $this->attendanceRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('attendance', array($id), "Impiger\Attendance\Models\Attendance");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->attendanceRepository->delete($attendance);
            
            event(new DeletedContentEvent(ATTENDANCE_MODULE_SCREEN_NAME, $request, $attendance));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        $dataExist = CrudHelper::isDependentDataExist('attendance', $ids, "Impiger\Attendance\Models\Attendance");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $attendance = $this->attendanceRepository->findOrFail($id);
            $this->attendanceRepository->delete($attendance);
            
            event(new DeletedContentEvent(ATTENDANCE_MODULE_SCREEN_NAME, $request, $attendance));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param ImportCustomFieldsAction $action
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function postImport(Request $request, BaseHttpResponse $response)
    {
           try {
            $request->validate([
                'file' => "required",
            ]);
            $bulkUpload = new BulkImport(new \Impiger\Attendance\Models\Attendance);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\Attendance\Models\Attendance')))." has been uploaded successfully");
            return $response->setMessage(trans('core/base::notices.bulk_upload_success_message'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors=[];
            foreach ($failures as $failure) {
                $error = $failure->values();
                $error['row'] = $failure->row();
                $error['error'] = implode(",",$failure->errors());
                if(false!==$key = array_search($failure->row(),array_column($errors,'row'))){
                    $errors[$key]['error'].="\r\n".implode(",",$failure->errors());
                }else{
                    $errors[]=$error;
                }
            }
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\Attendance\Models\Attendance')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\Attendance\Models\Attendance')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }

     /**
     * @customized By Ramesh Esakki
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws BindingResolutionException
     */
    public function postAttendanceData(Request $request, BaseHttpResponse $response)
    {
        $data = $request->all();
        $requestFilters = getRequestFilters();

        // dd($requestFilters);
       
        if (empty($data) || !is_array($data) || !$request->input('class')) {
            return $response
                            ->setError()
                            ->setMessage(trans('core/table::table.please_select_record'));
        }
        $repository = $this->attendanceRepository;
        \DB::beginTransaction();
        $tableName = $repository->getTable();
        $insertItem = [];
        $attendanceDate = Arr::get($data,'attendance_date');
        $day = Arr::get($data,'day');
        $entrepreneurIds = [];
        $attendanceStatus = ['present', 'absent'];

        $remarks = Arr::get($data,'remarks');
        // \Log::info(json_encode($remarks));

        $condition = array(
            'td.deleted_at' => NULL,
            // 'td.trainer_id' => $userId,
            't.financial_year_id'=> Arr::get($data,'financial_year_id'),
            't.annual_action_plan_id'=> Arr::get($data,'annual_action_plan_id'),
            't.training_title_id'=> Arr::get($data,'training_title_id')
        );

        foreach($attendanceStatus as $attendance) {
            if(Arr::get($data, $attendance)) {
                foreach ($data[$attendance] as $entrepreneurId => $value) {
                    $entrepreneurDetails = \Impiger\Entrepreneur\Models\Entrepreneur::where(['id' => $entrepreneurId])->get()->first();
                    $entrepreneurIds[] = $entrepreneurId;
                    $existsData = \Impiger\Attendance\Models\Attendance::where(['attendance_date'=> $attendanceDate, 'entrepreneur_id' => $entrepreneurId, 'training_title_id' => Arr::get($data,'training_title_id')])->get()->first();
                    $rowData = [];
                    $userRemark = Arr::get($remarks,$entrepreneurId);
                    // \Log::info("entrepreneurId");
                    // \Log::info($entrepreneurId);
                    // \Log::info("userRemark");
                    // \Log::info($userRemark);
                    if(!$existsData) {
                        $rowData['entrepreneur_id'] = $entrepreneurId;
                        $rowData['attendance_date'] = $attendanceDate;
                        $rowData['financial_year_id'] = Arr::get($data,'financial_year_id');
                        $rowData['annual_action_plan_id'] = Arr::get($data,'annual_action_plan_id');
                        $rowData['training_title_id'] = $trainingTitleId = Arr::get($data,'training_title_id');
                        $rowData['updated_at'] =  date('Y-m-d H:i:s');
                        $rowData['remark'] =  ($userRemark) ? $userRemark : null;
                        // $rowData['timetable_id'] =  $timetableId;
                        
                    } else {
                        
                        $rowData = $existsData;
                        if($userRemark && $existsData->remark != $userRemark) {
                            $rowData['remark'] =  ($userRemark) ? $userRemark : null;
                        }
                    }
                    
                    if($attendance == "present") {
                        $rowData['present'] = $value;
                        $rowData['absent'] = ($value == 1) ? 0: 1;
                    } else {
                        $rowData['absent'] = $value;
                        $rowData['present'] = ($value == 1) ? 0: 1;
                    }
                    
                    // \Log::info(json_encode($rowData));                                     
                    // $this->removeDuplicateEntry($repository, $entrepreneurId, $attendanceDate, $trainingTitleId);
                    // $repository->createOrUpdate($rowData, ['entrepreneur_id' => $entrepreneurId, 'attendance_date' => $attendanceDate, 'training_title_id' => $trainingTitleId]);
                    $repository->createOrUpdate($rowData);

                    
                    // $remarkRepository = $this->attendanceRemarkRepository;
                    // $remarksHistory = app(AttendanceRemarkInterface::class)
                    $makeCondition = [
                        'entrepreneur_id' => Arr::get($rowData,'entrepreneur_id'),
                        'training_title_id' => Arr::get($rowData,'training_title_id'),
                        'remark' => Arr::get($rowData,'remark')
                    ];
                    $remarksHistory = \Impiger\Attendance\Models\AttendanceRemark::where($makeCondition)->orderBy('id', 'desc')->first();
                    if(!$remarksHistory) {
                        $makeCondition['created_by'] = \Auth::id();
                        $this->attendanceRemarkRepository->createOrUpdate($makeCondition);
                    }

                }
            }
        }
        \DB::commit();
        return $response->setMessage(trans('core/table::table.save_bulk_change_success'));
    }

    public function removeDuplicateEntry($repository, $entrepreneurId, $date, $trainingTitleId)
    {
       if($trainingTitleId) {
            $existingData = $repository->deleteBy(['attendance_date' => $date, 'entrepreneur_id' => $entrepreneurId, 'training_title_id' => $trainingTitleId]);

        } else {
            $repository->deleteBy(['attendance_date' => $date, 'entrepreneur_id' => $entrepreneurId]);
        }
    }

    function getAnnualActionPlanList(Request $request, BaseHttpResponse $response) {
        if ($request->has('financial_year_id') && $request->input('financial_year_id')) {
            $financialYearId = $request->input('financial_year_id');
            $condition = array(
                'ap.deleted_at' => NULL,
                'ap.financial_year_id' => $financialYearId
            );
            $query = DB::table('annual_action_plan AS ap')->select("ap.id as id", \DB::raw("ap.name as text"));
            $query->where($condition);
            $query->orderBy("ap.id", "ASC");
            $query->groupBy("text");
            return $query->get()->toArray();
        } else {
            return $response->setError(true)
                ->setMessage("Required param missing");
        }
    }

    function getTrainingProgramList(Request $request, BaseHttpResponse $response) {
        if ($request->has('annual_action_plan_id') && $request->input('annual_action_plan_id')) {
            $annualActionPlanId = $request->input('annual_action_plan_id');
            $condition = array(
                'tp.deleted_at' => NULL,
                'tp.annual_action_plan_id' => $annualActionPlanId
            );
            $query = DB::table('training_title AS tp')->select("tp.id as id", \DB::raw("concat(tp.name,' - ',tp.code) as text"));
            $query->where($condition);
            // $query->where('tp.training_end_date', '<=', DB::Raw('Now()'));
            $query->orderBy("tp.id", "ASC");
            $query->groupBy("text");
            return $query->get()->toArray();
        } else {
            return $response->setError(true)
                ->setMessage("Required param missing");
        }
    }

    function getTrainingProgramSchedule(Request $request, BaseHttpResponse $response) {
        $programSchedule = DB::table('training_title')->select("id", \DB::raw("concat(code,' - ',venue) as text"),"training_end_date AS end_date", "training_start_date AS start_date")->where('id', $request->get('id'))->get()->toArray();
        if($programSchedule) {
            return $programSchedule;
        } else {
            return $response->setError(true)
            ->setMessage("selected program schedule is missing");
        }
        
    }

}
