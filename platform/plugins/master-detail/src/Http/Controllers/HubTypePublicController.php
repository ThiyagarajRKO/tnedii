<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\HubTypeRequest;
use Impiger\MasterDetail\Repositories\Interfaces\HubTypeInterface;
use Impiger\MasterDetail\Tables\HubTypeTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class HubTypePublicController extends Controller
{
    /**
     * @var HubTypeInterface
     */
    protected $hubTypeRepository;

    /**
     * @param HubTypeInterface $hubTypeRepository
     */
    public function __construct(HubTypeInterface $hubTypeRepository)
    {
        $this->hubTypeRepository = $hubTypeRepository;
    }

    /**
     * @param HubTypeTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(HubTypeTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param HubTypeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(HubTypeRequest $request, BaseHttpResponse $response)
    {
        try {
            $hubType = $this->hubTypeRepository->getModel();
            $table = $hubType->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $hubType->fillable(array_merge($hubType->getFillable(),["is_enabled"]));
                $hubType->is_enabled = 0;
            }
            $hubType->fill($request->input());
            $this->hubTypeRepository->createOrUpdate($hubType);
            
            CrudHelper::uploadFiles($request, $hubType);
            event(new CreatedContentEvent(HUB_TYPE_MODULE_SCREEN_NAME, $request, $hubType));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=hub type'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/hubType::failed_msg'));
        }
    }

    /**
     * @param HubTypeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, HubTypeRequest $request, BaseHttpResponse $response)
    {
        try {
            $hubType = $this->hubTypeRepository->findOrFail($id);
            
            $hubType->fill($request->input());
            $this->hubTypeRepository->createOrUpdate($hubType);
            event(new UpdatedContentEvent(HUB_TYPE_MODULE_SCREEN_NAME, $request, $hubType));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=hub type'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/hubType::failed_msg'));
        }
    }
}
