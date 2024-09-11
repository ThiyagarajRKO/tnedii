<?php

namespace Impiger\Usergroups\Forms;

use Assets;
use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Usergroups\Http\Requests\UsergroupsRequest;
use Impiger\Usergroups\Models\Usergroups;
use Impiger\ACL\Models\Role;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;

class UsergroupsForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $pathInfo = $this->request->getPathInfo();
        if((isset($this->formOptions['isView']) && $this->formOptions['isView']) || str_contains($pathInfo, 'viewdetail')) {
            return $this->viewForm();
        }
        Assets::addStyles(['jquery-ui'])
            ->addScripts(['jquery-ui'])
            ->addScriptsDirectly(['vendor/core/core/base/libraries/customListBox.js'])
            ->addStylesDirectly('vendor/core/core/acl/css/custom-style.css');
        $roles = Role::get();
        $rawCondition = get_common_condition('roles');
        if(!empty($rawCondition)){
            $roles = Role::whereRaw($rawCondition)->get();
        }
        $availableRoles = $this->getRoles();
        $mappedRoles = [];

        if ($this->getModel()) {
            $mappedRoles = $this->getModel()->roles;
        }
        $this
            ->setFormOption('template', 'plugins/crud::module.form-template') 
            ->setupModel(new Usergroups)
            ->setValidatorClass(UsergroupsRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => 'User Group Name',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'textarea', [
                    'label' => trans('core/base::forms.description'),
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => [
                        'rows' => 4,
                        'placeholder' => trans('core/base::forms.description_placeholder'),
                        
                    ],
                ])
                ->addMetaBoxes([
                'roles' => [
                    'title'   => trans('plugins/usergroups::usergroups.form.roles'),
                    'content' => view('plugins/usergroups::role-lists',compact('roles','availableRoles','mappedRoles'))->render(),
                ],
            ])->setActionButtons(view('plugins/crud::module.form-actions')->render());
    }
    protected function getRoles(){
        $mappedRoleIds=[];
        $availableRoles=[];
        $roles = Role::get();
        $rawCondition = get_common_condition('roles');
        if(!empty($rawCondition)){
            $roles = Role::whereRaw($rawCondition)->get();
        }
        $userGroups = Usergroups::get();
        foreach($userGroups as $userGroup){
            $mappedRoleIds[] = $userGroup->roles;
        }
        $mappedRoleIds = Arr::collapse($mappedRoleIds);
        foreach($roles as $role){
            if(!in_array($role->id, $mappedRoleIds)){
                $availableRoles[] = $role;
            }
        }
        return $availableRoles;
    }
    
    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {
        
		$this->model = (\Arr::get($this->model, 'usergroups')  && !\Arr::has($this->model, 'usergroups.0')) ?(object) $this->model['usergroups'] : $this->model;

        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new Usergroups)
            ->setValidatorClass(UsergroupsRequest::class)
            ->setTitle(page_title()->getTitle())
            ->withCustomFields() 
			->setFormOption('class','viewForm')
            
			->add("name" , "static", ["tag" => "div" , "label" => "User Group Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("description" , "static", ["tag" => "div" , "label" => "Description" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("roles" , "static", ["tag" => "div" ,'value' => CrudHelper::getMultiSelectText('roles','id', 'roles', 'name', 'deleted_at is  NULL', $this->model), "label" => "Roles" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
}
