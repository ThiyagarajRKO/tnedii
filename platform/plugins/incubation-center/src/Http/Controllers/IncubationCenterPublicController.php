<?php

namespace Impiger\IncubationCenter\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\IncubationCenter\Http\Requests\IncubationCenterRequest;
use Impiger\IncubationCenter\Repositories\Interfaces\IncubationCenterInterface;
use Impiger\IncubationCenter\Tables\IncubationCenterTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class IncubationCenterPublicController extends Controller
{
    /**
     * @var IncubationCenterInterface
     */
    protected $incubationCenterRepository;

    /**
     * @param IncubationCenterInterface $incubationCenterRepository
     */
    public function __construct(IncubationCenterInterface $incubationCenterRepository)
    {
        $this->incubationCenterRepository = $incubationCenterRepository;
    }

    /**
     * @param IncubationCenterTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(IncubationCenterTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param IncubationCenterRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(IncubationCenterRequest $request, BaseHttpResponse $response)
    {
        try {
            $incubationCenter = $this->incubationCenterRepository->getModel();
            $table = $incubationCenter->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $incubationCenter->fillable(array_merge($incubationCenter->getFillable(),["is_enabled"]));
                $incubationCenter->is_enabled = 0;
            }
            $incubationCenter->fill($request->input());
            $this->incubationCenterRepository->createOrUpdate($incubationCenter);
            
            CrudHelper::uploadFiles($request, $incubationCenter);
            event(new CreatedContentEvent(INCUBATION_CENTER_MODULE_SCREEN_NAME, $request, $incubationCenter));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=incubation center'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/incubationCenter::failed_msg'));
        }
    }

    /**
     * @param IncubationCenterRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, IncubationCenterRequest $request, BaseHttpResponse $response)
    {
        try {
            $incubationCenter = $this->incubationCenterRepository->findOrFail($id);
            
            $incubationCenter->fill($request->input());
            $this->incubationCenterRepository->createOrUpdate($incubationCenter);
            event(new UpdatedContentEvent(INCUBATION_CENTER_MODULE_SCREEN_NAME, $request, $incubationCenter));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=incubation center'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/incubationCenter::failed_msg'));
        }
    }
}
