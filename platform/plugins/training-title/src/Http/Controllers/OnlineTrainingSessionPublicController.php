<?php

namespace Impiger\TrainingTitle\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\TrainingTitle\Http\Requests\OnlineTrainingSessionRequest;
use Impiger\TrainingTitle\Repositories\Interfaces\OnlineTrainingSessionInterface;
use Impiger\TrainingTitle\Tables\OnlineTrainingSessionTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class OnlineTrainingSessionPublicController extends Controller
{
    /**
     * @var OnlineTrainingSessionInterface
     */
    protected $onlineTrainingSessionRepository;

    /**
     * @param OnlineTrainingSessionInterface $onlineTrainingSessionRepository
     */
    public function __construct(OnlineTrainingSessionInterface $onlineTrainingSessionRepository)
    {
        $this->onlineTrainingSessionRepository = $onlineTrainingSessionRepository;
    }

    /**
     * @param OnlineTrainingSessionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(OnlineTrainingSessionTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param OnlineTrainingSessionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(OnlineTrainingSessionRequest $request, BaseHttpResponse $response)
    {
        try {
            $onlineTrainingSession = $this->onlineTrainingSessionRepository->getModel();
            $table = $onlineTrainingSession->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $onlineTrainingSession->fillable(array_merge($onlineTrainingSession->getFillable(),["is_enabled"]));
                $onlineTrainingSession->is_enabled = 0;
            }
            $onlineTrainingSession->fill($request->input());
            $this->onlineTrainingSessionRepository->createOrUpdate($onlineTrainingSession);
            
            CrudHelper::uploadFiles($request, $onlineTrainingSession);
            event(new CreatedContentEvent(ONLINE_TRAINING_SESSION_MODULE_SCREEN_NAME, $request, $onlineTrainingSession));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=online training session'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/onlineTrainingSession::failed_msg'));
        }
    }

    /**
     * @param OnlineTrainingSessionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, OnlineTrainingSessionRequest $request, BaseHttpResponse $response)
    {
        try {
            $onlineTrainingSession = $this->onlineTrainingSessionRepository->findOrFail($id);
            
            $onlineTrainingSession->fill($request->input());
            $this->onlineTrainingSessionRepository->createOrUpdate($onlineTrainingSession);
            event(new UpdatedContentEvent(ONLINE_TRAINING_SESSION_MODULE_SCREEN_NAME, $request, $onlineTrainingSession));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=online training session'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/onlineTrainingSession::failed_msg'));
        }
    }
}
