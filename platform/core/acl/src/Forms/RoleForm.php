<?php

namespace Impiger\ACL\Forms;

use Assets;
use Impiger\ACL\Http\Requests\RoleCreateRequest;
use Impiger\ACL\Models\Role;
use Impiger\Base\Forms\FormAbstract;
use Illuminate\Support\Arr;
/* @Customized by Sabari Shankar.Parthiban */
use Illuminate\Support\Facades\Auth;
use App\Utils\CrudHelper;

class RoleForm extends FormAbstract {

    /**
     * {@inheritDoc}
     * @customized Sabari Shankar.Parthiban
     */
    public function buildForm() {
        /* @Customized By Sabari Shankar parthiban start */
        $pathInfo = $this->request->getPathInfo();
        if ((isset($this->formOptions['isView']) && $this->formOptions['isView']) || str_contains($pathInfo, 'viewdetail')) {
            return $this->viewForm();
        }
        /* @Customized By Sabari Shankar parthiban End */
        Assets::addStyles(['jquery-ui', 'jqueryTree'])
                ->addScripts(['jquery-ui', 'jqueryTree'])
                ->addScriptsDirectly(['vendor/core/core/base/libraries/customListBox.js'])
                ->addScriptsDirectly(['vendor/core/plugins/crud/js/crud_utils.js'])
                ->addScriptsDirectly('vendor/core/core/acl/js/role.js')
                ->addStylesDirectly(['vendor/core/core/acl/css/custom-style.css',
                    'vendor/core/plugins/crud/css/module_custom_styles.css']);

        $flags = $this->getAvailablePermissions();
        $children = $this->getPermissionTree($flags);
        $active = [];
        $mappedRoles = [];
        $readOnly = false;

        if ($this->getModel()) {
            $active = array_keys($this->getModel()->permissions);
            $mappedRoles = $this->getModel()->child_roles;
            $mappedRoles = \Arr::has($mappedRoles, 0) ? $mappedRoles : [];
            $isSystem = $this->getModel()->is_system;
            $readOnly = ($isSystem) ? true : false;
        }
         $wrapperCls = '';
        if(setting('role_form_custom_layout')){
           $wrapperCls='form-group col-md-4';
          }

        $this
                ->setupModel(new Role)
                ->setValidatorClass(RoleCreateRequest::class)
                ->withCustomFields();

                if(setting('role_form_custom_layout')){
                    $this->setFormOption('template', 'module.form-template')
                    ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
                    ->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout role'>
                            <fieldset><div class='row'>"]);
                }
                $this->add('name', 'text', [
                    'label' => trans('core/base::forms.name'),
                    'label_attr' => ['class' => 'control-label required'],
                    'attr' => [
                        'placeholder' => trans('core/base::forms.name_placeholder'),
                        'data-counter' => 120,
                        'readonly' => $readOnly
                    ],'wrapper' => ['class' => $wrapperCls]
                ])
                ->add('description', 'textarea', [
                    'label' => trans('core/base::forms.description'),
                    'label_attr' => ['class' => 'control-label required'],
                    'attr' => [
                        'rows' => 4,
                        'placeholder' => trans('core/base::forms.description_placeholder'),
                        'data-counter' => 255,
                    ],'wrapper' => ['class' => $wrapperCls]
        ]);
        /*  @customized Sabari Shankar.Parthiban start */


        $user = Auth::user();
        if ($user && $user->isSuperUser()) {
            $this->add('is_default', 'onOff', [
                        'label' => trans('core/base::forms.is_default'),
                        'label_attr' => ['class' => 'control-label'],
                        'default_value' => false,
                    ])
                    ->add('is_admin', 'onOff', [
                        'label' => trans('core/base::forms.is_admin'),
                        'label_attr' => ['class' => 'control-label'],
                        'default_value' => false,
            ]);
        }
        if (setting('enable_dls') && $user && ($user->isSuperUser() || $user->is_admin)) {
            $this->add('is_system', 'onOff', [
                'label' => trans('core/acl::permissions.is_system'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => false,
                'wrapper' => ['class' => $wrapperCls]
            ]);
        }
        if (setting('enable_dls') && $user && $user->applyDatalevelSecurity()) {
            $this->add("entity_type", "hidden", [
                        "label" => "Entity Type",
                        "label_attr" => ["class" => "control-label"],
                        "value" => CrudHelper::getEntityValue(),
                    ])
                    ->add("entity_id", "customSelect", [
                        "label" => CrudHelper::getLabelName(),
                        "label_attr" => ["class" => "control-label  "],
                        "attr" => ["class" => "select-full"],
                        "choices" => CrudHelper::getSelectOptionValues('entity', '', '', '', '', '', '', $this->model, 'entity_type'),
                        "empty_value" => "Select"])
            ;
            CrudHelper::isEntityCheck($this);
        }
        if(setting('role_form_custom_layout')){
            $this->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
            ->add("custom_html_main_close" , "html", ["html" => "</div>"]);
        }

        $roles = app(\Impiger\ACL\Roles::class)->getChildRolesUsingLogin(false);

        $this->addMetaBoxes([
                    'permissions' => [
                        'title' => trans('core/acl::permissions.permissions'),
                        'content' => view('core/acl::roles.permissions-lists', compact('active', 'flags', 'children'))->render(),
                    ],
                    'role_hierarchy' => [
                        'title' => 'Role Hierarchy',
                        'content' => view('core/acl::roles.role-hierarchy', compact('roles', 'mappedRoles'))->render(),
                    ]
                ])
                ->setActionButtons(view('module.form-actions')->render());
//            ->setActionButtons(view('core/acl::roles.actions', ['role' => $this->getModel()])->render());
        /*  @customized Sabari Shankar.Parthiban end */
    }

    /**
     * @return array
     */
    protected function getAvailablePermissions(): array {
        $permissions = [];

        $configuration = config(strtolower('cms-permissions'));
        if (!empty($configuration)) {
            foreach ($configuration as $config) {
                $permissions[$config['flag']] = $config;
            }
        }

        $types = ['core', 'packages', 'plugins'];

        foreach ($types as $type) {
            $permissions = array_merge($permissions, $this->getAvailablePermissionForEachType($type));
        }

        return $permissions;
    }

    /**
     * @param string $type
     * @return array
     * @customized Sabari Shankar.Parthiban
     */
    protected function getAvailablePermissionForEachType($type) {
        $permissions = [];
        $userPermissions = [];
        // available roles for the user
        $user = auth()->user();
        if (!$user->super_user) {
            $userPermissions = $user->permissions;
        }
        foreach (scan_folder(platform_path($type)) as $module) {
            $configuration = config(strtolower($type . '.' . $module . '.permissions'));
            if (!empty($configuration)) {
                foreach ($configuration as $config) {
                    if (!empty($userPermissions)) {
                        if (array_key_exists($config['flag'], $userPermissions)) {
                            $permissions[$config['flag']] = $config;
                        }
                    } else {
                        $permissions[$config['flag']] = $config;
                    }
                }
            }
        }

        return $permissions;
    }

    /**
     * @param array $permissions
     * @return array
     */
    protected function getPermissionTree($permissions): array {
        $sortedFlag = $permissions;
        sort($sortedFlag);
        $children['root'] = $this->getChildren('root', $sortedFlag);

        foreach (array_keys($permissions) as $key) {
            $childrenReturned = $this->getChildren($key, $permissions);
            if (count($childrenReturned) > 0) {
                $children[$key] = $childrenReturned;
            }
        }

        return $children;
    }

    /**
     * @param int $parentId
     * @param array $allFlags
     * @return mixed
     */
    protected function getChildren($parentId, array $allFlags) {
        $newFlagArray = [];
        foreach ($allFlags as $flagDetails) {
            if (Arr::get($flagDetails, 'parent_flag', 'root') == $parentId) {
                $newFlagArray[] = $flagDetails['flag'];
            }
        }
        return $newFlagArray;
    }

    /**
     * @Customized by Sabari Shankar Parthiban start
     * {@inheritDoc}
     */
    public function viewForm() {

        $this->model = (\Arr::get($this->model, 'role') && !\Arr::has($this->model, 'role.0')) ? (object) $this->model['usergroups'] : $this->model;

        $this
                ->setFormOption('template', 'core/base::forms.form-modal')
                ->setupModel(new Role)
                ->setValidatorClass(RoleCreateRequest::class)
                ->setTitle(page_title()->getTitle())
                ->withCustomFields()
                ->setFormOption('class', 'viewForm')
                ->add("name", "static", ["tag" => "div", "label" => "Role Name", "label_attr" => ["class" => "control-label "], 'attr' => ['class' => 'customStaticCls']])
                ->add("description", "static", ["tag" => "div", "label" => "Description", "label_attr" => ["class" => "control-label "], 'attr' => ['class' => 'customStaticCls']])
                ->add("is_system", "static", ["tag" => "div", "value" => CrudHelper::formatRows($this->model->is_system, 'radio', '1:Yes,0:No,:No', $this->model, ''), "label" => "System", "label_attr" => ["class" => "control-label "], 'attr' => ['class' => 'customStaticCls']]);
               if(!$this->model->is_admin){
                $this->add("child_roles", "static", ["tag" => "div", 'value' => CrudHelper::getMultiSelectText('roles', 'id', 'child_roles', 'name', 'deleted_at is  NULL', $this->model), "label" => "Child Roles", "label_attr" => ["class" => "control-label "], 'attr' => ['class' => 'customStaticCls']]);
               }
    }

    /* @Customized by Sabari Shankar Parthiban end */
}
