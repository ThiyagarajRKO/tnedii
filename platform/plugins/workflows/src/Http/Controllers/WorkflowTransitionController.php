<?php

namespace Impiger\Workflows\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Workflows\Http\Requests\WorkflowTransitionRequest;
use Impiger\Workflows\Repositories\Interfaces\WorkflowTransitionInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Workflows\Tables\WorkflowTransitionTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Workflows\Forms\WorkflowTransitionForm;
use Impiger\Base\Forms\FormBuilder;

class WorkflowTransitionController extends BaseController
{
    /**
     * @var WorkflowTransitionInterface
     */
    protected $workflowTransitionRepository;

    /**
     * @param WorkflowTransitionInterface $workflowTransitionRepository
     */
    public function __construct(WorkflowTransitionInterface $workflowTransitionRepository)
    {
        $this->workflowTransitionRepository = $workflowTransitionRepository;
    }

    /**
     * @param WorkflowTransitionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(WorkflowTransitionTable $table)
    {
        page_title()->setTitle(trans('plugins/workflows::workflow-transition.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/workflows::workflow-transition.create'));

        return $formBuilder->create(WorkflowTransitionForm::class)->renderForm();
    }

    /**
     * @param WorkflowTransitionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(WorkflowTransitionRequest $request, BaseHttpResponse $response)
    {
        $input =array_merge($request->input(),['slug'=> \Str::slug($request->input('name'))]);
        $workflowTransition = $this->workflowTransitionRepository->createOrUpdate($input);

        event(new CreatedContentEvent(WORKFLOW_TRANSITION_MODULE_SCREEN_NAME, $request, $workflowTransition));

        return $response
            ->setPreviousUrl(route('workflow-transition.index'))
            ->setNextUrl(route('workflow-transition.edit', $workflowTransition->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $workflowTransition = $this->workflowTransitionRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $workflowTransition));

        page_title()->setTitle(trans('plugins/workflows::workflow-transition.edit') . ' "' . $workflowTransition->name . '"');

        return $formBuilder->create(WorkflowTransitionForm::class, ['model' => $workflowTransition])->renderForm();
    }

    /**
     * @param int $id
     * @param WorkflowTransitionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, WorkflowTransitionRequest $request, BaseHttpResponse $response)
    {
        $workflowTransition = $this->workflowTransitionRepository->findOrFail($id);

        $workflowTransition->fill($request->input());

        $this->workflowTransitionRepository->createOrUpdate($workflowTransition);

        event(new UpdatedContentEvent(WORKFLOW_TRANSITION_MODULE_SCREEN_NAME, $request, $workflowTransition));

        return $response
            ->setPreviousUrl(route('workflow-transition.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $workflowTransition = $this->workflowTransitionRepository->findOrFail($id);

            $this->workflowTransitionRepository->delete($workflowTransition);

            event(new DeletedContentEvent(WORKFLOW_TRANSITION_MODULE_SCREEN_NAME, $request, $workflowTransition));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $workflowTransition = $this->workflowTransitionRepository->findOrFail($id);
            $this->workflowTransitionRepository->delete($workflowTransition);
            event(new DeletedContentEvent(WORKFLOW_TRANSITION_MODULE_SCREEN_NAME, $request, $workflowTransition));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
