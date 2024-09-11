<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\MasterDetailRequest;
use Impiger\MasterDetail\Models\MasterDetail;
use DB;
use App\Utils\CrudHelper;

class MasterDetailForm extends FormAbstract
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
            
            ->setupModel(new MasterDetail)
            ->setValidatorClass(MasterDetailRequest::class)
            ->withCustomFields()
            ->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("attribute" , "customSelect", ["label" => "Attribute", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'attribute', 'attribute', 'deleted_at IS NULL', '', $this->model, '', $this->getName(), ''),"empty_value" => "Select", "rules" => "required"])
			->add("name" , "text", ["label" => "Name", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
//			->add("slug" , "text", ["label" => "Slug", "label_attr" => ["class" => "control-label  "],'attr' => ['data-field_index' => '0'], "rules" => ""])
			
            ->setActionButtons(view('plugins/crud::module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {
        
		$this->model = (\Arr::get($this->model, 'master_details')  && !\Arr::has($this->model, 'master_details.0')) ?(object) $this->model['master_details'] : $this->model;

        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new MasterDetail)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(MasterDetailRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            
			->add("attribute" , "static", ["tag" => "div" , "label" => "Attribute" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("name" , "static", ["tag" => "div" , "label" => "Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
//			->add("slug" , "static", ["tag" => "div" , "label" => "Slug" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
    
}
