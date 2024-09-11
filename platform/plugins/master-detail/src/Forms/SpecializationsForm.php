<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\SpecializationsRequest;
use Impiger\MasterDetail\Models\Specializations;
use DB;
use App\Utils\CrudHelper;

class SpecializationsForm extends FormAbstract
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
            ->setupModel(new Specializations)
            ->setValidatorClass(SpecializationsRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout specializations'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])
			->add("name" , "text", ["label" => "Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-8'],'attr' => ['data-field_index' => '2'], "rules" => "required"])
			->add("industry_id" , "customSelect", ["label" => "Industry", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '3'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="industries"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-8'],"empty_value" => "Select", "rules" => "required"])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'specializations')  && !\Arr::has($this->model, 'specializations.0')) ?(object) $this->model['specializations'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = Specializations::getModel();
        }

        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new Specializations)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(SpecializationsRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout specializations'>
                    <fieldset><div class='row'>"])
			
			->add("name" , "static", ["tag" => "div" , "label" => "Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("industry_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->industry_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Industry" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
