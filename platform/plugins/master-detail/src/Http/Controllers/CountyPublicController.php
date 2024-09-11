<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\CountyRequest;
use Impiger\MasterDetail\Repositories\Interfaces\CountyInterface;
use Impiger\MasterDetail\Tables\CountyTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class CountyPublicController extends Controller
{
    /**
     * @var CountyInterface
     */
    protected $countyRepository;

    /**
     * @param CountyInterface $countyRepository
     */
    public function __construct(CountyInterface $countyRepository)
    {
        $this->countyRepository = $countyRepository;
    }

    /**
     * @param CountyTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(CountyTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param CountyRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(CountyRequest $request, BaseHttpResponse $response)
    {
        try {
            $county = $this->countyRepository->getModel();
            $table = $county->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $county->fillable(array_merge($county->getFillable(),["is_enabled"]));
                $county->is_enabled = 0;
            }
            $county->fill($request->input());
            $this->countyRepository->createOrUpdate($county);
            
            CrudHelper::uploadFiles($request, $county);
            event(new CreatedContentEvent(COUNTY_MODULE_SCREEN_NAME, $request, $county));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=county'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/county::failed_msg'));
        }
    }

    /**
     * @param CountyRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, CountyRequest $request, BaseHttpResponse $response)
    {
        try {
            $county = $this->countyRepository->findOrFail($id);
            
            $county->fill($request->input());
            $this->countyRepository->createOrUpdate($county);
            event(new UpdatedContentEvent(COUNTY_MODULE_SCREEN_NAME, $request, $county));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=county'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/county::failed_msg'));
        }
    }
}
