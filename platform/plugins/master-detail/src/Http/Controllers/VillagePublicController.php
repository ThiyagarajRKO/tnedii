<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\VillageRequest;
use Impiger\MasterDetail\Repositories\Interfaces\VillageInterface;
use Impiger\MasterDetail\Tables\VillageTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class VillagePublicController extends Controller
{
    /**
     * @var VillageInterface
     */
    protected $villageRepository;

    /**
     * @param VillageInterface $villageRepository
     */
    public function __construct(VillageInterface $villageRepository)
    {
        $this->villageRepository = $villageRepository;
    }

    /**
     * @param VillageTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(VillageTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param VillageRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(VillageRequest $request, BaseHttpResponse $response)
    {
        try {
            $village = $this->villageRepository->getModel();
            $table = $village->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $village->fillable(array_merge($village->getFillable(),["is_enabled"]));
                $village->is_enabled = 0;
            }
            $village->fill($request->input());
            $this->villageRepository->createOrUpdate($village);
            
            CrudHelper::uploadFiles($request, $village);
            event(new CreatedContentEvent(VILLAGE_MODULE_SCREEN_NAME, $request, $village));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=village'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/village::failed_msg'));
        }
    }

    /**
     * @param VillageRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, VillageRequest $request, BaseHttpResponse $response)
    {
        try {
            $village = $this->villageRepository->findOrFail($id);
            
            $village->fill($request->input());
            $this->villageRepository->createOrUpdate($village);
            event(new UpdatedContentEvent(VILLAGE_MODULE_SCREEN_NAME, $request, $village));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=village'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/village::failed_msg'));
        }
    }
}
