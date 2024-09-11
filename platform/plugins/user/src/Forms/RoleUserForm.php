<?php

namespace Impiger\User\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\User\Http\Requests\RoleUserRequest;
use Impiger\User\Models\RoleUser;
use DB;
use App\Utils\CrudHelper;

class RoleUserForm extends FormAbstract
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

        $this   
            
            ->setupModel(new RoleUser)
            ->setValidatorClass(RoleUserRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => ""])
			->add("custom_html_open_0" , "html", ["html" => "<div>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],])
			->add("role_id" , "customSelect", ["label" => "Role", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-field_index' => '2','multiple' => true,'restrict_based_on' => 'true','disabled' => CrudHelper::isFieldDisabled('edit')],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'roles', 'id', 'name', '', '', $this->model, '', $this->getName()), "rules" => "sometimes|required"])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div>"])
			->add("custom_html_main_close" , "html", ["html" => ""])
			
            ->setActionButtons(view('plugins/crud::module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {
        
		$this->model = (\Arr::get($this->model, 'role_users')  && !\Arr::has($this->model, 'role_users.0')) ?(object) $this->model['role_users'] : $this->model;

        $this
            
            ->setupModel(new RoleUser)
            ->setValidatorClass(RoleUserRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => ""])
			->add("custom_html_open_0" , "html", ["html" => "<div>"])
			
			->add("role_id" , "static", ["tag" => "div" ,'value' => CrudHelper::getMultiSelectText('roles','id', 'role_id', 'name', '', $this->model), "label" => "Role" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div>"])
			->add("custom_html_main_close" , "html", ["html" => ""])
			;
    }
}
