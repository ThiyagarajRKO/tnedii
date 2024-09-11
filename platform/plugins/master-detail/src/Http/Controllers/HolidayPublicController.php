<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\HolidayRequest;
use Impiger\MasterDetail\Repositories\Interfaces\HolidayInterface;
use Impiger\MasterDetail\Tables\HolidayTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class HolidayPublicController extends Controller
{
    /**
     * @var HolidayInterface
     */
    protected $holidayRepository;

    /**
     * @param HolidayInterface $holidayRepository
     */
    public function __construct(HolidayInterface $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;
    }

    /**
     * @param HolidayTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(HolidayTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param HolidayRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(HolidayRequest $request, BaseHttpResponse $response)
    {
        try {
            $holiday = $this->holidayRepository->getModel();
            $table = $holiday->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $holiday->fillable(array_merge($holiday->getFillable(),["is_enabled"]));
                $holiday->is_enabled = 0;
            }
            $holiday->fill($request->input());
            $this->holidayRepository->createOrUpdate($holiday);
            
            CrudHelper::uploadFiles($request, $holiday);
            event(new CreatedContentEvent(HOLIDAY_MODULE_SCREEN_NAME, $request, $holiday));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=holiday'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/holiday::failed_msg'));
        }
    }

    /**
     * @param HolidayRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, HolidayRequest $request, BaseHttpResponse $response)
    {
        try {
            $holiday = $this->holidayRepository->findOrFail($id);
            
            $holiday->fill($request->input());
            $this->holidayRepository->createOrUpdate($holiday);
            event(new UpdatedContentEvent(HOLIDAY_MODULE_SCREEN_NAME, $request, $holiday));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=holiday'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/holiday::failed_msg'));
        }
    }
}
