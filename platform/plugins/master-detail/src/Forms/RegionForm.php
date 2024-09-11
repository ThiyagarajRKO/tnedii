<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\RegionRequest;
use Impiger\MasterDetail\Models\Region;
use DB;
use App\Utils\CrudHelper;


class RegionForm extends FormAbstract
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
            ->setFormOption('template','module.form-template')
            ->setupModel(new Region)
            ->setValidatorClass(RegionRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout region'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("name" , "text", ["label" => "Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '2'], "rules" => "required"])
			->add("code" , "text", ["label" => "Code", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("description" , "textarea", ["label" => "Description", "label_attr" => ["class" => "control-label  "], "attr"=>["rows" => 4,'data-field_index' => '4'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'regions')  && !\Arr::has($this->model, 'regions.0')) ?(object) $this->model['regions'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = Region::getModel();
        }
        
        
        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new Region)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(RegionRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout region'>
                    <fieldset><div class='row'>"])
			
			->add("name" , "static", ["tag" => "div" , "label" => "Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("code" , "static", ["tag" => "div" , "label" => "Code" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("description" , "static", ["tag" => "div" , "label" => "Description" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
