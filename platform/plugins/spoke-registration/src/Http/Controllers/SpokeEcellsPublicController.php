<?php

namespace Impiger\SpokeRegistration\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\SpokeRegistration\Http\Requests\SpokeEcellsRequest;
use Impiger\SpokeRegistration\Repositories\Interfaces\SpokeEcellsInterface;
use Impiger\SpokeRegistration\Tables\SpokeEcellsTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class SpokeEcellsPublicController extends Controller
{
    /**
     * @var SpokeEcellsInterface
     */
    protected $spokeEcellsRepository;

    /**
     * @param SpokeEcellsInterface $spokeEcellsRepository
     */
    public function __construct(SpokeEcellsInterface $spokeEcellsRepository)
    {
        $this->spokeEcellsRepository = $spokeEcellsRepository;
    }

    /**
     * @param SpokeEcellsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SpokeEcellsTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param SpokeEcellsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(SpokeEcellsRequest $request, BaseHttpResponse $response)
    {
        try {
            $spokeEcells = $this->spokeEcellsRepository->getModel();
            $table = $spokeEcells->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $spokeEcells->fillable(array_merge($spokeEcells->getFillable(),["is_enabled"]));
                $spokeEcells->is_enabled = 0;
            }
            $spokeEcells->fill($request->input());
            $this->spokeEcellsRepository->createOrUpdate($spokeEcells);
            
            CrudHelper::uploadFiles($request, $spokeEcells);
            event(new CreatedContentEvent(SPOKE_ECELLS_MODULE_SCREEN_NAME, $request, $spokeEcells));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=spoke ecells'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/spokeEcells::failed_msg'));
        }
    }

    /**
     * @param SpokeEcellsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, SpokeEcellsRequest $request, BaseHttpResponse $response)
    {
        try {
            $spokeEcells = $this->spokeEcellsRepository->findOrFail($id);
            
            $spokeEcells->fill($request->input());
            $this->spokeEcellsRepository->createOrUpdate($spokeEcells);
            event(new UpdatedContentEvent(SPOKE_ECELLS_MODULE_SCREEN_NAME, $request, $spokeEcells));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=spoke ecells'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/spokeEcells::failed_msg'));
        }
    }
}
