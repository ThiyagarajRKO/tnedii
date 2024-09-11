<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\MilestoneRequest;
use Impiger\MasterDetail\Repositories\Interfaces\MilestoneInterface;
use Impiger\MasterDetail\Tables\MilestoneTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class MilestonePublicController extends Controller
{
    /**
     * @var MilestoneInterface
     */
    protected $milestoneRepository;

    /**
     * @param MilestoneInterface $milestoneRepository
     */
    public function __construct(MilestoneInterface $milestoneRepository)
    {
        $this->milestoneRepository = $milestoneRepository;
    }

    /**
     * @param MilestoneTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MilestoneTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param MilestoneRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(MilestoneRequest $request, BaseHttpResponse $response)
    {
        try {
            $milestone = $this->milestoneRepository->getModel();
            $table = $milestone->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $milestone->fillable(array_merge($milestone->getFillable(),["is_enabled"]));
                $milestone->is_enabled = 0;
            }
            $milestone->fill($request->input());
            $this->milestoneRepository->createOrUpdate($milestone);
            
            CrudHelper::uploadFiles($request, $milestone);
            event(new CreatedContentEvent(MILESTONE_MODULE_SCREEN_NAME, $request, $milestone));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=milestone'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/milestone::failed_msg'));
        }
    }

    /**
     * @param MilestoneRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, MilestoneRequest $request, BaseHttpResponse $response)
    {
        try {
            $milestone = $this->milestoneRepository->findOrFail($id);
            
            $milestone->fill($request->input());
            $this->milestoneRepository->createOrUpdate($milestone);
            event(new UpdatedContentEvent(MILESTONE_MODULE_SCREEN_NAME, $request, $milestone));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=milestone'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/milestone::failed_msg'));
        }
    }
}
