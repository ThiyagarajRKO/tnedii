<?php

namespace Impiger\MsmeCandidateDetails\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MsmeCandidateDetails\Http\Requests\MsmeCandidateDetailsRequest;
use Impiger\MsmeCandidateDetails\Repositories\Interfaces\MsmeCandidateDetailsInterface;
use Impiger\MsmeCandidateDetails\Tables\MsmeCandidateDetailsTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class MsmeCandidateDetailsPublicController extends Controller
{
    /**
     * @var MsmeCandidateDetailsInterface
     */
    protected $msmeCandidateDetailsRepository;

    /**
     * @param MsmeCandidateDetailsInterface $msmeCandidateDetailsRepository
     */
    public function __construct(MsmeCandidateDetailsInterface $msmeCandidateDetailsRepository)
    {
        $this->msmeCandidateDetailsRepository = $msmeCandidateDetailsRepository;
    }

    /**
     * @param MsmeCandidateDetailsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MsmeCandidateDetailsTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param MsmeCandidateDetailsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(MsmeCandidateDetailsRequest $request, BaseHttpResponse $response)
    {
        try {
            $msmeCandidateDetails = $this->msmeCandidateDetailsRepository->getModel();
            $table = $msmeCandidateDetails->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $msmeCandidateDetails->fillable(array_merge($msmeCandidateDetails->getFillable(),["is_enabled"]));
                $msmeCandidateDetails->is_enabled = 0;
            }
            $msmeCandidateDetails->fill($request->input());
            $this->msmeCandidateDetailsRepository->createOrUpdate($msmeCandidateDetails);
            
            CrudHelper::uploadFiles($request, $msmeCandidateDetails);
            event(new CreatedContentEvent(MSME_CANDIDATE_DETAILS_MODULE_SCREEN_NAME, $request, $msmeCandidateDetails));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=msme candidate details'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/msmeCandidateDetails::failed_msg'));
        }
    }

    /**
     * @param MsmeCandidateDetailsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, MsmeCandidateDetailsRequest $request, BaseHttpResponse $response)
    {
        try {
            $msmeCandidateDetails = $this->msmeCandidateDetailsRepository->findOrFail($id);
            
            $msmeCandidateDetails->fill($request->input());
            $this->msmeCandidateDetailsRepository->createOrUpdate($msmeCandidateDetails);
            event(new UpdatedContentEvent(MSME_CANDIDATE_DETAILS_MODULE_SCREEN_NAME, $request, $msmeCandidateDetails));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=msme candidate details'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/msmeCandidateDetails::failed_msg'));
        }
    }
}
