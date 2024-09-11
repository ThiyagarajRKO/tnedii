<?php

namespace Impiger\Attendance\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Attendance\Http\Requests\AttendanceRemarkRequest;
use Impiger\Attendance\Repositories\Interfaces\AttendanceRemarkInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Attendance\Tables\AttendanceRemarkTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Attendance\Forms\AttendanceRemarkForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class AttendanceRemarkController extends BaseController
{
    /**
     * @var AttendanceRemarkInterface
     */
    protected $attendanceRemarkRepository;

    /**
     * @param AttendanceRemarkInterface $attendanceRemarkRepository
     */
    public function __construct(AttendanceRemarkInterface $attendanceRemarkRepository)
    {
        $this->attendanceRemarkRepository = $attendanceRemarkRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param AttendanceRemarkTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AttendanceRemarkTable $table)
    {
        page_title()->setTitle(trans('plugins/attendance::attendance-remark.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/attendance::attendance-remark.create'));

        return $formBuilder->create(AttendanceRemarkForm::class)->renderForm();
    }

    /**
     * @param AttendanceRemarkRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(AttendanceRemarkRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $attendanceRemark = $this->attendanceRemarkRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(ATTENDANCE_REMARK_MODULE_SCREEN_NAME, $request, $attendanceRemark));
        
        
        return $response
            ->setPreviousUrl(route('attendance-remark.index'))
            ->setNextUrl(route('attendance-remark.edit', $attendanceRemark->id))
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
        
        $attendanceRemark = $this->attendanceRemarkRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $attendanceRemark));

        $name = ($attendanceRemark->name) ? ' "' . $attendanceRemark->name . '"' : "";
        page_title()->setTitle(trans('plugins/attendance::attendance-remark.edit') . $name);

        return $formBuilder->create(AttendanceRemarkForm::class, ['model' => $attendanceRemark])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $attendanceRemark = $this->attendanceRemarkRepository->findOrFail($id);
        $name = ($attendanceRemark->name) ? ' "' . $attendanceRemark->name . '"' : "";
        page_title()->setTitle(trans('plugins/attendance::attendance-remark.view') . $name);

        return $formBuilder->create(AttendanceRemarkForm::class, ['model' => $attendanceRemark, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param AttendanceRemarkRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, AttendanceRemarkRequest $request, BaseHttpResponse $response )
    {
        
        $attendanceRemark = $this->attendanceRemarkRepository->findOrFail($id);
        
        
        $attendanceRemark->fill($request->input());

        $this->attendanceRemarkRepository->createOrUpdate($attendanceRemark);

        event(new UpdatedContentEvent(ATTENDANCE_REMARK_MODULE_SCREEN_NAME, $request, $attendanceRemark));
        
        
        return $response
            ->setPreviousUrl(route('attendance-remark.index'))
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
            $attendanceRemark = $this->attendanceRemarkRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('attendance-remark', array($id), "Impiger\Attendance\Models\AttendanceRemark");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->attendanceRemarkRepository->delete($attendanceRemark);
            
            event(new DeletedContentEvent(ATTENDANCE_REMARK_MODULE_SCREEN_NAME, $request, $attendanceRemark));

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

        $dataExist = CrudHelper::isDependentDataExist('attendance-remark', $ids, "Impiger\Attendance\Models\AttendanceRemark");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $attendanceRemark = $this->attendanceRemarkRepository->findOrFail($id);
            $this->attendanceRemarkRepository->delete($attendanceRemark);
            
            event(new DeletedContentEvent(ATTENDANCE_REMARK_MODULE_SCREEN_NAME, $request, $attendanceRemark));
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
            $bulkUpload = new BulkImport(new \Impiger\Attendance\Models\AttendanceRemark);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\Attendance\Models\AttendanceRemark')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\Attendance\Models\AttendanceRemark')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\Attendance\Models\AttendanceRemark')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
