<?php

namespace Impiger\IncubationCenter\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\IncubationCenter\Http\Requests\IncubationCenterRequest;
use Impiger\IncubationCenter\Models\IncubationCenter;
use DB;
use App\Utils\CrudHelper;

class IncubationCenterForm extends FormAbstract
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
            ->setupModel(new IncubationCenter)
            ->setValidatorClass(IncubationCenterRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout incubation_center'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])
			->add("district_id" , "customSelect", ["label" => "District", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'district', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("center_name" , "text", ["label" => "Center Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("manager_name" , "text", ["label" => "Manager Name", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '4'], "rules" => ""])
			->add("establishment_date" , "date", ["label" => "Establishment Date", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => "required"])
			->add("no_of_incubatees" , "text", ["label" => "No Of Incubatees", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '6'], "rules" => "required"])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'incubation_centers')  && !\Arr::has($this->model, 'incubation_centers.0')) ?(object) $this->model['incubation_centers'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = IncubationCenter::getModel();
        }

        $this
            
            ->setupModel(new IncubationCenter)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(IncubationCenterRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout incubation_center'>
                    <fieldset><div class='row'>"])
			
			->add("district_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->district_id, 'database', 'district|id|name', $this->model, ''), "label" => "District" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("center_name" , "static", ["tag" => "div" , "label" => "Center Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("manager_name" , "static", ["tag" => "div" , "label" => "Manager Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("establishment_date" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->establishment_date), "label" => "Establishment Date" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("no_of_incubatees" , "static", ["tag" => "div" , "label" => "No Of Incubatees" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
