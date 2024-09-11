<?php

namespace Impiger\HubInstitution\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\HubInstitution\Http\Requests\HubInstitutionRequest;
use Impiger\HubInstitution\Repositories\Interfaces\HubInstitutionInterface;
use Impiger\HubInstitution\Tables\HubInstitutionTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class HubInstitutionPublicController extends Controller
{
    /**
     * @var HubInstitutionInterface
     */
    protected $hubInstitutionRepository;

    /**
     * @param HubInstitutionInterface $hubInstitutionRepository
     */
    public function __construct(HubInstitutionInterface $hubInstitutionRepository)
    {
        $this->hubInstitutionRepository = $hubInstitutionRepository;
    }

    /**
     * @param HubInstitutionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(HubInstitutionTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param HubInstitutionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(HubInstitutionRequest $request, BaseHttpResponse $response)
    {
        try {
            $hubInstitution = $this->hubInstitutionRepository->getModel();
            $table = $hubInstitution->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $hubInstitution->fillable(array_merge($hubInstitution->getFillable(),["is_enabled"]));
                $hubInstitution->is_enabled = 0;
            }
            $hubInstitution->fill($request->input());
            $this->hubInstitutionRepository->createOrUpdate($hubInstitution);
            
            CrudHelper::uploadFiles($request, $hubInstitution);
            event(new CreatedContentEvent(HUB_INSTITUTION_MODULE_SCREEN_NAME, $request, $hubInstitution));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=hub institution'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/hubInstitution::failed_msg'));
        }
    }

    /**
     * @param HubInstitutionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, HubInstitutionRequest $request, BaseHttpResponse $response)
    {
        try {
            $hubInstitution = $this->hubInstitutionRepository->findOrFail($id);
            
            $hubInstitution->fill($request->input());
            $this->hubInstitutionRepository->createOrUpdate($hubInstitution);
            event(new UpdatedContentEvent(HUB_INSTITUTION_MODULE_SCREEN_NAME, $request, $hubInstitution));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=hub institution'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/hubInstitution::failed_msg'));
        }
    }
}
