<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\CountryRequest;
use Impiger\MasterDetail\Repositories\Interfaces\CountryInterface;
use Impiger\MasterDetail\Tables\CountryTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class CountryPublicController extends Controller
{
    /**
     * @var CountryInterface
     */
    protected $countryRepository;

    /**
     * @param CountryInterface $countryRepository
     */
    public function __construct(CountryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param CountryTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(CountryTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param CountryRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(CountryRequest $request, BaseHttpResponse $response)
    {
        try {
            $country = $this->countryRepository->getModel();
            $table = $country->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $country->fillable(array_merge($country->getFillable(),["is_enabled"]));
                $country->is_enabled = 0;
            }
            $country->fill($request->input());
            $this->countryRepository->createOrUpdate($country);
            
            CrudHelper::uploadFiles($request, $country);
            event(new CreatedContentEvent(COUNTRY_MODULE_SCREEN_NAME, $request, $country));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=country'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/country::failed_msg'));
        }
    }

    /**
     * @param CountryRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, CountryRequest $request, BaseHttpResponse $response)
    {
        try {
            $country = $this->countryRepository->findOrFail($id);
            
            $country->fill($request->input());
            $this->countryRepository->createOrUpdate($country);
            event(new UpdatedContentEvent(COUNTRY_MODULE_SCREEN_NAME, $request, $country));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=country'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/country::failed_msg'));
        }
    }
}
