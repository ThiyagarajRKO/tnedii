<?php

namespace Impiger\Mentee\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Mentee\Http\Requests\MenteeRequest;
use Impiger\Mentee\Repositories\Interfaces\MenteeInterface;
use Impiger\Mentee\Tables\MenteeTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class MenteePublicController extends Controller
{
    /**
     * @var MenteeInterface
     */
    protected $menteeRepository;

    /**
     * @param MenteeInterface $menteeRepository
     */
    public function __construct(MenteeInterface $menteeRepository)
    {
        $this->menteeRepository = $menteeRepository;
    }

    /**
     * @param MenteeTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MenteeTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param MenteeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(MenteeRequest $request, BaseHttpResponse $response)
    {
        try {
            $mentee = $this->menteeRepository->getModel();
            $table = $mentee->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $mentee->fillable(array_merge($mentee->getFillable(),["is_enabled"]));
                $mentee->is_enabled = 0;
            }
            $mentee->fill($request->input());
            $this->menteeRepository->createOrUpdate($mentee);
            
            CrudHelper::uploadFiles($request, $mentee);
            event(new CreatedContentEvent(MENTEE_MODULE_SCREEN_NAME, $request, $mentee));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=mentee'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/mentee::failed_msg'));
        }
    }

    /**
     * @param MenteeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, MenteeRequest $request, BaseHttpResponse $response)
    {
        try {
            $mentee = $this->menteeRepository->findOrFail($id);
            
            $mentee->fill($request->input());
            $this->menteeRepository->createOrUpdate($mentee);
            event(new UpdatedContentEvent(MENTEE_MODULE_SCREEN_NAME, $request, $mentee));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=mentee'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/mentee::failed_msg'));
        }
    }
}
