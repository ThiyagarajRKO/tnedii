<?php

namespace Impiger\Attendance\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Attendance\Http\Requests\AttendanceRemarkRequest;
use Impiger\Attendance\Repositories\Interfaces\AttendanceRemarkInterface;
use Impiger\Attendance\Tables\AttendanceRemarkTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class AttendanceRemarkPublicController extends Controller
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
    }

    /**
     * @param AttendanceRemarkTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AttendanceRemarkTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param AttendanceRemarkRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(AttendanceRemarkRequest $request, BaseHttpResponse $response)
    {
        try {
            $attendanceRemark = $this->attendanceRemarkRepository->getModel();
            $table = $attendanceRemark->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $attendanceRemark->fillable(array_merge($attendanceRemark->getFillable(),["is_enabled"]));
                $attendanceRemark->is_enabled = 0;
            }
            $attendanceRemark->fill($request->input());
            $this->attendanceRemarkRepository->createOrUpdate($attendanceRemark);
            
            CrudHelper::uploadFiles($request, $attendanceRemark);
            event(new CreatedContentEvent(ATTENDANCE_REMARK_MODULE_SCREEN_NAME, $request, $attendanceRemark));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=attendance remark'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/attendanceRemark::failed_msg'));
        }
    }

    /**
     * @param AttendanceRemarkRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, AttendanceRemarkRequest $request, BaseHttpResponse $response)
    {
        try {
            $attendanceRemark = $this->attendanceRemarkRepository->findOrFail($id);
            
            $attendanceRemark->fill($request->input());
            $this->attendanceRemarkRepository->createOrUpdate($attendanceRemark);
            event(new UpdatedContentEvent(ATTENDANCE_REMARK_MODULE_SCREEN_NAME, $request, $attendanceRemark));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=attendance remark'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/attendanceRemark::failed_msg'));
        }
    }
}
