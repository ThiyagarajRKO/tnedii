<?php

namespace Impiger\Workflows\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Workflows\Http\Requests\WorkflowPermissionRequest;
use Impiger\Workflows\Repositories\Interfaces\WorkflowPermissionInterface;
use Impiger\Workflows\Repositories\Interfaces\WorkflowsInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Workflows\Tables\WorkflowPermissionTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Workflows\Forms\WorkflowPermissionForm;
use Impiger\Base\Forms\FormBuilder;
use Impiger\Workflows\Support\WorkflowsSupport;
use Arr;

class WorkflowPermissionController extends BaseController
{
    /**
     * @var WorkflowPermissionInterface
     */
    protected $workflowPermissionRepository;

    /**
     * @var WorkflowsInterface
     */
    protected $workflowsRepository;

    /**
     * @param WorkflowPermissionInterface $workflowPermissionRepository
     */
    public function __construct(WorkflowPermissionInterface $workflowPermissionRepository, WorkflowsInterface $workflowsRepository)
    {
        $this->workflowPermissionRepository = $workflowPermissionRepository;
        $this->workflowsRepository = $workflowsRepository;
    }

    /**
     * @param WorkflowPermissionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(WorkflowPermissionTable $table)
    {
        page_title()->setTitle(trans('plugins/workflows::workflow-permission.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/workflows::workflow-permission.create'));

        return $formBuilder->create(WorkflowPermissionForm::class)->renderForm();
    }

    /**
     * @param WorkflowPermissionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(WorkflowPermissionRequest $request, BaseHttpResponse $response)
    {
        $workflowPermission = $this->workflowPermissionRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(WORKFLOW_PERMISSION_MODULE_SCREEN_NAME, $request, $workflowPermission));

        return $response
            ->setPreviousUrl(route('workflow-permission.index'))
            ->setNextUrl(route('workflow-permission.edit', $workflowPermission->id))
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
        $workflowPermission = $this->workflowPermissionRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $workflowPermission));

        page_title()->setTitle(trans('plugins/workflows::workflow-permission.edit') . ' "' . $workflowPermission->name . '"');

        return $formBuilder->create(WorkflowPermissionForm::class, ['model' => $workflowPermission])->renderForm();
    }

    /**
     * @param int $id
     * @param WorkflowPermissionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, WorkflowPermissionRequest $request, BaseHttpResponse $response)
    {
        $workflows = $this->workflowsRepository->findOrFail($id);
        $input = $request->input();
        $rolePermissions = Arr::get($input,'role_permissions');
        $transitions = WorkflowsSupport::getWorkflowAllTransitions($workflows->module_controller);

        foreach ($transitions as $k => $trans) {
            $data = $where = [];
            $data['workflows_id'] = $where['workflows_id'] = $input['workflows_id'];
            $data['transition'] = $where['transition'] = $k;
            $roles = Arr::get($rolePermissions,$k);
            $data['role_permissions'] = $roles;
            $data['reference_id'] = $where['reference_id'] = 1;
            $data['reference_type'] = $where['reference_type'] = 'Impiger\Institution\Models\Institution';
            $this->workflowPermissionRepository->createOrUpdate($data, $where);
        }

        event(new UpdatedContentEvent(WORKFLOW_PERMISSION_MODULE_SCREEN_NAME, $request, $workflows));

        return $response
            ->setPreviousUrl(route('workflow-permission.index'))
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
            $workflowPermission = $this->workflowPermissionRepository->findOrFail($id);

            $this->workflowPermissionRepository->delete($workflowPermission);

            event(new DeletedContentEvent(WORKFLOW_PERMISSION_MODULE_SCREEN_NAME, $request, $workflowPermission));

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
            $workflowPermission = $this->workflowPermissionRepository->findOrFail($id);
            $this->workflowPermissionRepository->delete($workflowPermission);
            event(new DeletedContentEvent(WORKFLOW_PERMISSION_MODULE_SCREEN_NAME, $request, $workflowPermission));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
