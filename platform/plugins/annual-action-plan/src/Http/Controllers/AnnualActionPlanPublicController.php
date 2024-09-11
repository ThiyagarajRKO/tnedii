<?php

namespace Impiger\AnnualActionPlan\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\AnnualActionPlan\Http\Requests\AnnualActionPlanRequest;
use Impiger\AnnualActionPlan\Repositories\Interfaces\AnnualActionPlanInterface;
use Impiger\AnnualActionPlan\Tables\AnnualActionPlanTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class AnnualActionPlanPublicController extends Controller
{
    /**
     * @var AnnualActionPlanInterface
     */
    protected $annualActionPlanRepository;

    /**
     * @param AnnualActionPlanInterface $annualActionPlanRepository
     */
    public function __construct(AnnualActionPlanInterface $annualActionPlanRepository)
    {
        $this->annualActionPlanRepository = $annualActionPlanRepository;
    }

    /**
     * @param AnnualActionPlanTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AnnualActionPlanTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param AnnualActionPlanRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(AnnualActionPlanRequest $request, BaseHttpResponse $response)
    {
        try {
            $annualActionPlan = $this->annualActionPlanRepository->getModel();
            $table = $annualActionPlan->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $annualActionPlan->fillable(array_merge($annualActionPlan->getFillable(),["is_enabled"]));
                $annualActionPlan->is_enabled = 0;
            }
            $annualActionPlan->fill($request->input());
            $this->annualActionPlanRepository->createOrUpdate($annualActionPlan);
            
            CrudHelper::uploadFiles($request, $annualActionPlan);
            event(new CreatedContentEvent(ANNUAL_ACTION_PLAN_MODULE_SCREEN_NAME, $request, $annualActionPlan));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=annual action plan'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/annualActionPlan::failed_msg'));
        }
    }

    /**
     * @param AnnualActionPlanRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, AnnualActionPlanRequest $request, BaseHttpResponse $response)
    {
        try {
            $annualActionPlan = $this->annualActionPlanRepository->findOrFail($id);
            
            $annualActionPlan->fill($request->input());
            $this->annualActionPlanRepository->createOrUpdate($annualActionPlan);
            event(new UpdatedContentEvent(ANNUAL_ACTION_PLAN_MODULE_SCREEN_NAME, $request, $annualActionPlan));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=annual action plan'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/annualActionPlan::failed_msg'));
        }
    }
}
