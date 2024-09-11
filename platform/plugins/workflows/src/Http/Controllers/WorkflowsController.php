<?php

namespace Impiger\Workflows\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Workflows\Http\Requests\WorkflowsRequest;
use Impiger\Workflows\Http\Requests\WorkflowPermissionRequest;
use Impiger\Workflows\Repositories\Interfaces\WorkflowsInterface;
use Impiger\Workflows\Repositories\Interfaces\WorkflowPermissionInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Workflows\Tables\WorkflowsTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Workflows\Forms\WorkflowsForm;
use Impiger\Workflows\Forms\WorkflowPermissionForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use Impiger\Workflows\Support\WorkflowsSupport;
use Arr;
use App\Utils\CrudHelper;

class WorkflowsController extends BaseController
{
    /**
     * @var WorkflowsInterface
     */
    protected $workflowsRepository;

    /**
     * @var WorkflowPermissionInterface
     */
    protected $workflowPermissionRepository;

    /**
     * @param WorkflowsInterface $workflowsRepository
     */
    public function __construct(WorkflowsInterface $workflowsRepository, WorkflowPermissionInterface $workflowPermissionRepository)
    {
        $this->workflowsRepository = $workflowsRepository;
        $this->workflowPermissionRepository = $workflowPermissionRepository;
    }

    /**
     * @param WorkflowsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(WorkflowsTable $table)
    {
        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/crud_utils.js'
        ]);
        page_title()->setTitle(trans('plugins/workflows::workflows.name'));
        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/workflows::workflows.create'));

        Assets::addStylesDirectly([
            'vendor/core/plugins/custom-field/css/custom-field.css',
            'vendor/core/plugins/custom-field/css/edit-field-group.css',
        ])
            ->addScriptsDirectly('vendor/core/plugins/custom-field/js/edit-field-group.js')
            ->addScripts(['jquery-ui']);

        return $formBuilder->create(WorkflowsForm::class)->renderForm();
    }

    /**
     * @param WorkflowsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(WorkflowsRequest $request, BaseHttpResponse $response)
    {
        $input =array_merge($request->input(),['slug'=> \Str::slug($request->input('name'))]); 
        $moduleControllerExist = $this->workflowsRepository->getFirstBy(['module_controller' => $request->input('module_controller'), 'is_enabled' => 1]);
        if(!$moduleControllerExist) {
            $input =array_merge($request->input(),['is_enabled'=> 1]); 
        }
        $workflows = $this->workflowsRepository->createOrUpdate($input);
        $transitions = [];
        foreach($request->input('transitions') as $trans) {
            $trans['slug'] = \Str::slug($trans['name']);
            $transitions[] = $trans;
        }

        
        $request['transitions'] = $transitions;
        CrudHelper::createUpdateSubformsHasMany($request, $workflows, 'transitions');
        event(new CreatedContentEvent(WORKFLOWS_MODULE_SCREEN_NAME, $request, $workflows));

        return $response
            ->setPreviousUrl(route('workflows.index'))
            ->setNextUrl(route('workflows.edit', $workflows->id))
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
        $workflows = $this->workflowsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $workflows));

        page_title()->setTitle(trans('plugins/workflows::workflows.edit') . ' "' . $workflows->name . '"');

        return $formBuilder->create(WorkflowsForm::class, ['model' => $workflows])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function getWorkflowPermission($id, FormBuilder $formBuilder, Request $request)
    {
        $workflows = $this->workflowsRepository->findOrFail($id);
        page_title()->setTitle(trans('plugins/workflows::workflows.edit') . ' "' . $workflows->name . '"');

        return $formBuilder->create(WorkflowPermissionForm::class, ['model' => $workflows])->renderForm();
    }

    /**
     * @param int $id
     * @param WorkflowsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, WorkflowsRequest $request, BaseHttpResponse $response)
    {

        $workflows = $this->workflowsRepository->findOrFail($id);
        CrudHelper::createUpdateSubformsHasMany($request, $workflows, 'transitions', $id, '');
        $workflows->fill($request->input());
        $this->workflowsRepository->createOrUpdate($workflows);
        event(new UpdatedContentEvent(WORKFLOW_PERMISSION_MODULE_SCREEN_NAME, $request, $workflows));

        return $response
            ->setPreviousUrl(route('workflows.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param WorkflowsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function updateWorkflowPermission($id, WorkflowPermissionRequest $request, BaseHttpResponse $response)
    {
        $workflows = $this->workflowsRepository->findOrFail($id);
        $input = $request->input();
        $permissions = Arr::get($input,'user_permissions');
        
        $workflows->email_subject = $request->input('email_subject');
        $workflows->email_content = $request->input('email_content');
        $this->workflowsRepository->createOrUpdate($workflows);
        
        $transitions = WorkflowsSupport::getWorkflowAllTransitions($workflows->module_controller);

        foreach ($transitions as $k => $trans) {
            $data = $where = [];
            $data['workflows_id'] = $where['workflows_id'] = $input['workflows_id'];
            $data['transition'] = $where['transition'] = $k;
            $roles = Arr::get($permissions,$k);
            $data['user_permissions'] = $roles;
            $data['reference_id'] = $where['reference_id'] = 1;
            $data['reference_type'] = $where['reference_type'] = 'Impiger\Institution\Models\Institution';
            $permissionModel = $this->workflowPermissionRepository->createOrUpdate($data, $where);
            CrudHelper::createUpdateSubformsHasMany($request, $permissionModel, 'configs',$k);
        }
        CrudHelper::createUpdateSubformsHasMany($request, $workflows, 'workflow_meta_data',$input['workflows_id']);
        
        event(new UpdatedContentEvent(WORKFLOW_PERMISSION_MODULE_SCREEN_NAME, $request, $workflows));

        return $response
            ->setPreviousUrl(route('workflows.index'))
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
            $workflows = $this->workflowsRepository->findOrFail($id);

            $this->workflowsRepository->delete($workflows);

            event(new DeletedContentEvent(WORKFLOWS_MODULE_SCREEN_NAME, $request, $workflows));

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
            $workflows = $this->workflowsRepository->findOrFail($id);
            $this->workflowsRepository->delete($workflows);
            event(new DeletedContentEvent(WORKFLOWS_MODULE_SCREEN_NAME, $request, $workflows));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

   
    public function getWorkflowDetails(Request $request, $slug, BaseHttpResponse $response)
    {
        $workflowConfigs = config('plugins.workflows.workflow', []);

        if (isset($workflowConfigs[$slug])) {
            $transitions = $workflowConfigs[$slug]['transitions'];

            return $response
                ->setData($transitions);
        }

        return $response
            ->setError()
            ->setMessage(__('Workflow Module not supported!'));
    }

    public function updateRowActivation($id, Request $request, BaseHttpResponse $response)
    {
        $value = $request->input('value');
        $model = $request->input('model');
        $moduleName = $request->input('module');
        if (!$model) {
            return $response
                ->setError(true)
                ->setMessage('Required param missing.');
        }
        $modelObj = new $model;
        $rowData = $modelObj::find($id);
        $screen = class_basename($model);

        if (!empty($rowData)) {
            $rowData->is_enabled = ($value) ? 0 : 1;

            if($rowData->is_enabled) {
                $affectedRows = \DB::table('workflows')
                    ->where('module_controller', $rowData->module_controller)
                    ->where('id', '!=', $id)
                    ->update(['is_enabled' => 0]);
            }
            $message = ($rowData->name) ? $rowData->name . " " : $screen . " ";
            $message .= ($value) ? trans('plugins/crud::crud.disable_success_message') : trans('plugins/crud::crud.enable_success_message');
            
            if ($rowData->save()) {
                event(new UpdatedContentEvent($screen, $request, $rowData));
                return $response
                    ->setMessage($message);
            }
        }
    }
}
