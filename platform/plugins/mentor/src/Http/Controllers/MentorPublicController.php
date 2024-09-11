<?php

namespace Impiger\Mentor\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Mentor\Http\Requests\MentorRequest;
use Impiger\Mentor\Repositories\Interfaces\MentorInterface;
use Impiger\Mentor\Tables\MentorTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class MentorPublicController extends Controller
{
    /**
     * @var MentorInterface
     */
    protected $mentorRepository;

    /**
     * @param MentorInterface $mentorRepository
     */
    public function __construct(MentorInterface $mentorRepository)
    {
        $this->mentorRepository = $mentorRepository;
    }

    /**
     * @param MentorTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MentorTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param MentorRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(MentorRequest $request, BaseHttpResponse $response)
    {
        try {
            $mentor = $this->mentorRepository->getModel();
            $table = $mentor->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $mentor->fillable(array_merge($mentor->getFillable(),["is_enabled"]));
                $mentor->is_enabled = 0;
            }
            $mentor->fill($request->input());
            $this->mentorRepository->createOrUpdate($mentor);
            
            CrudHelper::uploadFiles($request, $mentor);
            event(new CreatedContentEvent(MENTOR_MODULE_SCREEN_NAME, $request, $mentor));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=mentor'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/mentor::failed_msg'));
        }
    }

    /**
     * @param MentorRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, MentorRequest $request, BaseHttpResponse $response)
    {
        try {
            $mentor = $this->mentorRepository->findOrFail($id);
            
            $mentor->fill($request->input());
            $this->mentorRepository->createOrUpdate($mentor);
            event(new UpdatedContentEvent(MENTOR_MODULE_SCREEN_NAME, $request, $mentor));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=mentor'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/mentor::failed_msg'));
        }
    }
}
