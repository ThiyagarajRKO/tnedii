<?php

namespace Impiger\InnovationVoucherProgram\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\InnovationVoucherProgram\Http\Requests\IvpCompanyDetailsRequest;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\IvpCompanyDetailsInterface;
use Impiger\InnovationVoucherProgram\Tables\IvpCompanyDetailsTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class IvpCompanyDetailsPublicController extends Controller
{
    /**
     * @var IvpCompanyDetailsInterface
     */
    protected $ivpCompanyDetailsRepository;

    /**
     * @param IvpCompanyDetailsInterface $ivpCompanyDetailsRepository
     */
    public function __construct(IvpCompanyDetailsInterface $ivpCompanyDetailsRepository)
    {
        $this->ivpCompanyDetailsRepository = $ivpCompanyDetailsRepository;
    }

    /**
     * @param IvpCompanyDetailsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(IvpCompanyDetailsTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param IvpCompanyDetailsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(IvpCompanyDetailsRequest $request, BaseHttpResponse $response)
    {
        try {
            $ivpCompanyDetails = $this->ivpCompanyDetailsRepository->getModel();
            $table = $ivpCompanyDetails->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $ivpCompanyDetails->fillable(array_merge($ivpCompanyDetails->getFillable(),["is_enabled"]));
                $ivpCompanyDetails->is_enabled = 0;
            }
            $ivpCompanyDetails->fill($request->input());
            $this->ivpCompanyDetailsRepository->createOrUpdate($ivpCompanyDetails);
            
            CrudHelper::uploadFiles($request, $ivpCompanyDetails);
            event(new CreatedContentEvent(IVP_COMPANY_DETAILS_MODULE_SCREEN_NAME, $request, $ivpCompanyDetails));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=ivp company details'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/ivpCompanyDetails::failed_msg'));
        }
    }

    /**
     * @param IvpCompanyDetailsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, IvpCompanyDetailsRequest $request, BaseHttpResponse $response)
    {
        try {
            $ivpCompanyDetails = $this->ivpCompanyDetailsRepository->findOrFail($id);
            
            $ivpCompanyDetails->fill($request->input());
            $this->ivpCompanyDetailsRepository->createOrUpdate($ivpCompanyDetails);
            event(new UpdatedContentEvent(IVP_COMPANY_DETAILS_MODULE_SCREEN_NAME, $request, $ivpCompanyDetails));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=ivp company details'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/ivpCompanyDetails::failed_msg'));
        }
    }
}
