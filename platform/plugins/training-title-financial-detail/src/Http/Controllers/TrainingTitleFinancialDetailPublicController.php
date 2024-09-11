<?php

namespace Impiger\TrainingTitleFinancialDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\TrainingTitleFinancialDetail\Http\Requests\TrainingTitleFinancialDetailRequest;
use Impiger\TrainingTitleFinancialDetail\Repositories\Interfaces\TrainingTitleFinancialDetailInterface;
use Impiger\TrainingTitleFinancialDetail\Tables\TrainingTitleFinancialDetailTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class TrainingTitleFinancialDetailPublicController extends Controller
{
    /**
     * @var TrainingTitleFinancialDetailInterface
     */
    protected $trainingTitleFinancialDetailRepository;

    /**
     * @param TrainingTitleFinancialDetailInterface $trainingTitleFinancialDetailRepository
     */
    public function __construct(TrainingTitleFinancialDetailInterface $trainingTitleFinancialDetailRepository)
    {
        $this->trainingTitleFinancialDetailRepository = $trainingTitleFinancialDetailRepository;
    }

    /**
     * @param TrainingTitleFinancialDetailTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TrainingTitleFinancialDetailTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param TrainingTitleFinancialDetailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(TrainingTitleFinancialDetailRequest $request, BaseHttpResponse $response)
    {
        try {
            $trainingTitleFinancialDetail = $this->trainingTitleFinancialDetailRepository->getModel();
            $table = $trainingTitleFinancialDetail->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $trainingTitleFinancialDetail->fillable(array_merge($trainingTitleFinancialDetail->getFillable(),["is_enabled"]));
                $trainingTitleFinancialDetail->is_enabled = 0;
            }
            $trainingTitleFinancialDetail->fill($request->input());
            $this->trainingTitleFinancialDetailRepository->createOrUpdate($trainingTitleFinancialDetail);
            
            CrudHelper::uploadFiles($request, $trainingTitleFinancialDetail);
            event(new CreatedContentEvent(TRAINING_TITLE_FINANCIAL_DETAIL_MODULE_SCREEN_NAME, $request, $trainingTitleFinancialDetail));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=training title financial detail'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/trainingTitleFinancialDetail::failed_msg'));
        }
    }

    /**
     * @param TrainingTitleFinancialDetailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, TrainingTitleFinancialDetailRequest $request, BaseHttpResponse $response)
    {
        try {
            $trainingTitleFinancialDetail = $this->trainingTitleFinancialDetailRepository->findOrFail($id);
            
            $trainingTitleFinancialDetail->fill($request->input());
            $this->trainingTitleFinancialDetailRepository->createOrUpdate($trainingTitleFinancialDetail);
            event(new UpdatedContentEvent(TRAINING_TITLE_FINANCIAL_DETAIL_MODULE_SCREEN_NAME, $request, $trainingTitleFinancialDetail));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=training title financial detail'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/trainingTitleFinancialDetail::failed_msg'));
        }
    }
}
