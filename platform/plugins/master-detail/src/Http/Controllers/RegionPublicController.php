<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\RegionRequest;
use Impiger\MasterDetail\Repositories\Interfaces\RegionInterface;
use Impiger\MasterDetail\Tables\RegionTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class RegionPublicController extends Controller
{
    /**
     * @var RegionInterface
     */
    protected $regionRepository;

    /**
     * @param RegionInterface $regionRepository
     */
    public function __construct(RegionInterface $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    /**
     * @param RegionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(RegionTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param RegionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(RegionRequest $request, BaseHttpResponse $response)
    {
        try {
            $region = $this->regionRepository->getModel();
            $table = $region->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $region->fillable(array_merge($region->getFillable(),["is_enabled"]));
                $region->is_enabled = 0;
            }
            $region->fill($request->input());
            $this->regionRepository->createOrUpdate($region);
            
            CrudHelper::uploadFiles($request, $region);
            event(new CreatedContentEvent(REGION_MODULE_SCREEN_NAME, $request, $region));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=region'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/region::failed_msg'));
        }
    }

    /**
     * @param RegionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, RegionRequest $request, BaseHttpResponse $response)
    {
        try {
            $region = $this->regionRepository->findOrFail($id);
            
            $region->fill($request->input());
            $this->regionRepository->createOrUpdate($region);
            event(new UpdatedContentEvent(REGION_MODULE_SCREEN_NAME, $request, $region));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=region'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/region::failed_msg'));
        }
    }
}
