<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\HubTypeRequest;
use Impiger\MasterDetail\Models\HubType;
use DB;
use App\Utils\CrudHelper;

class HubTypeForm extends FormAbstract
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
            
            ->setupModel(new HubType)
            ->setValidatorClass(HubTypeRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='gridLayout col-md-12'>
						<fieldset>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("hub_type" , "text", ["label" => "Hub Type", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '2'], "rules" => "required"])
			->add("hub_type_code" , "text", ["label" => "Hub Type Code", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</fieldset></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'hub_types')  && !\Arr::has($this->model, 'hub_types.0')) ?(object) $this->model['hub_types'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = HubType::getModel();
        }

        $this
            
            ->setupModel(new HubType)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(HubTypeRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='gridLayout col-md-12'>
						<fieldset>"])
			
			->add("hub_type" , "static", ["tag" => "div" , "label" => "Hub Type" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("hub_type_code" , "static", ["tag" => "div" , "label" => "Hub Type Code" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</fieldset></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
