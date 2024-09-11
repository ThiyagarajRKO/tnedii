<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\DistrictRequest;
use Impiger\MasterDetail\Repositories\Interfaces\DistrictInterface;
use Impiger\MasterDetail\Tables\DistrictTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class DistrictPublicController extends Controller
{
    /**
     * @var DistrictInterface
     */
    protected $districtRepository;

    /**
     * @param DistrictInterface $districtRepository
     */
    public function __construct(DistrictInterface $districtRepository)
    {
        $this->districtRepository = $districtRepository;
    }

    /**
     * @param DistrictTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(DistrictTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param DistrictRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(DistrictRequest $request, BaseHttpResponse $response)
    {
        try {
            $district = $this->districtRepository->getModel();
            $table = $district->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $district->fillable(array_merge($district->getFillable(),["is_enabled"]));
                $district->is_enabled = 0;
            }
            $district->fill($request->input());
            $this->districtRepository->createOrUpdate($district);
            
            CrudHelper::uploadFiles($request, $district);
            event(new CreatedContentEvent(DISTRICT_MODULE_SCREEN_NAME, $request, $district));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=district'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/district::failed_msg'));
        }
    }

    /**
     * @param DistrictRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, DistrictRequest $request, BaseHttpResponse $response)
    {
        try {
            $district = $this->districtRepository->findOrFail($id);
            
            $district->fill($request->input());
            $this->districtRepository->createOrUpdate($district);
            event(new UpdatedContentEvent(DISTRICT_MODULE_SCREEN_NAME, $request, $district));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=district'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/district::failed_msg'));
        }
    }
}
