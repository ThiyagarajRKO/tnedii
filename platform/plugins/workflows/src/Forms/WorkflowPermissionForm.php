<?php

namespace Impiger\Workflows\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Workflows\Http\Requests\WorkflowPermissionRequest;
use Impiger\Workflows\Models\WorkflowPermission;
use Impiger\Workflows\Models\Workflows;
use Impiger\Workflows\Support\WorkflowsSupport;
use Impiger\ACL\Models\Role;
use Illuminate\Support\Arr;
use Assets;


class WorkflowPermissionForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        Assets::addStyles(['jquery-ui'])
            ->addScripts(['jquery-ui'])
            ->addScriptsDirectly(['vendor/core/core/base/libraries/customListBox.js'])
            ->addStylesDirectly('vendor/core/core/acl/css/custom-style.css')
            ->addStylesDirectly([
                'vendor/core/core/base/libraries/codemirror/lib/codemirror.css',
                'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.css',
                'vendor/core/core/setting/css/setting.css',
                'vendor/core/plugins/crud/css/module_custom_styles.css',
            ])
            ->addScriptsDirectly([
                'vendor/core/core/base/libraries/codemirror/lib/codemirror.js',
                'vendor/core/core/base/libraries/codemirror/lib/css.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/anyword-hint.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/css-hint.js',
                'vendor/core/plugins/crud/js/crud_utils.js',
                'vendor/core/core/base/js/common_utils.js',
                'vendor/core/plugins/workflows/js/workflow.js'
            ]);
            

        $roles = Role::select('id', 'name')->get();
        $availableRoles = $roles;
        $mappedRoles = [];
        $workflowID = null;
        $workflow = $this->getModel();
        $transitions = [];
        $configs = [];
        if ($workflow) {
            $mappedRoles = [];
            $transitions = WorkflowsSupport::getWorkflowAllTransitions($workflow->module_controller);
            $transitionTitleMap = $workflow->transitions->pluck('name', 'id')->toArray();
            $mappedIds = $this->getMappedPermissionList($workflow->workflowPermissions, $workflow->permission_specific_to);
            $workflowPermissions = $workflow->workflowPermissions;
            if (!empty($workflowPermissions)) {
                foreach ($workflowPermissions as $idx => $workflowPermission) {
                    $configs[] = $this->getSubFormData($workflowPermission->configs, 'configs');
                }
            }
        }
        $emailContent = ($workflow->email_content) ?: get_setting_email_template_content('plugins', 'workflows', 'workflow_email');
        $emailSubject = ($workflow->email_subject) ?: $workflow->name . ' Approval Process';
        $mailConfig = unserialize(WORKFLOW_EMAIL_CONFIG_VARIABLES);
        $moduleFields = [];
        if (is_subclass_of($workflow->module_controller, 'Illuminate\Database\Eloquent\Model')) {
            $moduleController = new $workflow->module_controller();
            $moduleFields = $moduleController->getFillable();
        }
        
        $workflowStates = \CustomWorkflow::getWorkflowAllStates($workflow->module_controller);
        $metaData = ($workflow) ? $workflow->workflow_meta_data :[];        
        
        $this
            ->setFormOption('template', 'module.form-template')
            ->setupModel(new WorkflowPermission)
            ->withCustomFields()
            ->addMetaBoxes([
                'roles' => [
                    'searchPlaceholder' => true,
                    'searchClass' => 'searchState',
                    'title'   => $workflow->name . ' - Permissions ',
                    'content' => view('plugins/workflows::workflow-tabs', compact('roles', 'availableRoles', 'mappedIds', 'transitions', 'transitionTitleMap', 'workflow', 'emailSubject', 'emailContent', 'mailConfig', 'moduleFields', 'configs','metaData','workflowStates'))->render(),
                ],
            ])
           ->setActionButtons(view('module.form-actions')->render());
    }

    protected function getMappedPermissionList($permissions, $specificTo)
    {
        $mappedIds = [];

        foreach ($permissions as $permission) {
            $mappedIds[$permission->transition] = $permission->user_permissions;
        }
        
        return $mappedIds;
    }


    protected function getSubFormData($data, $subFormKey)
    {
        $results = [];
        if (!empty($data)) {
            foreach ($data as $index => $formData) {
                foreach ($formData->getOriginal() as $key => $value) {
                    $results[$index][$key] = $value;
                }
            }
        }
        return $results;
    }
}
