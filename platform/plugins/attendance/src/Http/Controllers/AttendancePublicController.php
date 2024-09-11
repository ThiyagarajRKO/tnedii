<?php

namespace Impiger\Attendance\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Attendance\Http\Requests\AttendanceRequest;
use Impiger\Attendance\Repositories\Interfaces\AttendanceInterface;
use Impiger\Attendance\Tables\AttendanceTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class AttendancePublicController extends Controller
{
    /**
     * @var AttendanceInterface
     */
    protected $attendanceRepository;

    /**
     * @param AttendanceInterface $attendanceRepository
     */
    public function __construct(AttendanceInterface $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    /**
     * @param AttendanceTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AttendanceTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param AttendanceRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(AttendanceRequest $request, BaseHttpResponse $response)
    {
        try {
            $attendance = $this->attendanceRepository->getModel();
            $table = $attendance->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $attendance->fillable(array_merge($attendance->getFillable(),["is_enabled"]));
                $attendance->is_enabled = 0;
            }
            $attendance->fill($request->input());
            $this->attendanceRepository->createOrUpdate($attendance);
            
            CrudHelper::uploadFiles($request, $attendance);
            event(new CreatedContentEvent(ATTENDANCE_MODULE_SCREEN_NAME, $request, $attendance));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=attendance'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/attendance::failed_msg'));
        }
    }

    /**
     * @param AttendanceRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, AttendanceRequest $request, BaseHttpResponse $response)
    {
        try {
            $attendance = $this->attendanceRepository->findOrFail($id);
            
            $attendance->fill($request->input());
            $this->attendanceRepository->createOrUpdate($attendance);
            event(new UpdatedContentEvent(ATTENDANCE_MODULE_SCREEN_NAME, $request, $attendance));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=attendance'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/attendance::failed_msg'));
        }
    }
}
