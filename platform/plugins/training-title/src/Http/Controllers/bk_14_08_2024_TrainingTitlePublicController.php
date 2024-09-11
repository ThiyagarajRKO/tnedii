<?php

namespace Impiger\TrainingTitle\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\TrainingTitle\Http\Requests\TrainingTitleRequest;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;
use Impiger\TrainingTitle\Tables\TrainingTitleTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class TrainingTitlePublicController extends Controller
{
    /**
     * @var TrainingTitleInterface
     */
    protected $trainingTitleRepository;

    /**
     * @param TrainingTitleInterface $trainingTitleRepository
     */
    public function __construct(TrainingTitleInterface $trainingTitleRepository)
    {
        $this->trainingTitleRepository = $trainingTitleRepository;
    }

    /**
     * @param TrainingTitleTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TrainingTitleTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param TrainingTitleRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(TrainingTitleRequest $request, BaseHttpResponse $response)
    {
        try {
            $trainingTitle = $this->trainingTitleRepository->getModel();
            $table = $trainingTitle->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $trainingTitle->fillable(array_merge($trainingTitle->getFillable(),["is_enabled"]));
                $trainingTitle->is_enabled = 0;
            }
            $trainingTitle->fill($request->input());
            $this->trainingTitleRepository->createOrUpdate($trainingTitle);
            
            CrudHelper::uploadFiles($request, $trainingTitle);
            event(new CreatedContentEvent(TRAINING_TITLE_MODULE_SCREEN_NAME, $request, $trainingTitle));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=training title'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/trainingTitle::failed_msg'));
        }
    }

    /**
     * @param TrainingTitleRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, TrainingTitleRequest $request, BaseHttpResponse $response)
    {
        try {
            $trainingTitle = $this->trainingTitleRepository->findOrFail($id);
            
            $trainingTitle->fill($request->input());
            $this->trainingTitleRepository->createOrUpdate($trainingTitle);
            event(new UpdatedContentEvent(TRAINING_TITLE_MODULE_SCREEN_NAME, $request, $trainingTitle));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=training title'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/trainingTitle::failed_msg'));
        }
    }
}
