<?php

namespace Impiger\Entrepreneur\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Entrepreneur\Http\Requests\TraineeRequest;
use Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface;
use Impiger\Entrepreneur\Tables\TraineeTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class TraineePublicController extends Controller
{
    /**
     * @var TraineeInterface
     */
    protected $traineeRepository;

    /**
     * @param TraineeInterface $traineeRepository
     */
    public function __construct(TraineeInterface $traineeRepository)
    {
        $this->traineeRepository = $traineeRepository;
    }

    /**
     * @param TraineeTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TraineeTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param TraineeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(TraineeRequest $request, BaseHttpResponse $response)
    {
        try {
            $trainee = $this->traineeRepository->getModel();
            $table = $trainee->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $trainee->fillable(array_merge($trainee->getFillable(),["is_enabled"]));
                $trainee->is_enabled = 0;
            }
            $trainee->fill($request->input());
            $this->traineeRepository->createOrUpdate($trainee);
            
            CrudHelper::uploadFiles($request, $trainee);
            event(new CreatedContentEvent(TRAINEE_MODULE_SCREEN_NAME, $request, $trainee));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=trainee'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/trainee::failed_msg'));
        }
    }

    /**
     * @param TraineeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, TraineeRequest $request, BaseHttpResponse $response)
    {
        try {
            $trainee = $this->traineeRepository->findOrFail($id);
            
            $trainee->fill($request->input());
            $this->traineeRepository->createOrUpdate($trainee);
            event(new UpdatedContentEvent(TRAINEE_MODULE_SCREEN_NAME, $request, $trainee));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=trainee'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/trainee::failed_msg'));
        }
    }
}
