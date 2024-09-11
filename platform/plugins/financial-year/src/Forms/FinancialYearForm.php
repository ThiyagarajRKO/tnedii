<?php

namespace Impiger\FinancialYear\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\FinancialYear\Http\Requests\FinancialYearRequest;
use Impiger\FinancialYear\Models\FinancialYear;
use DB;
use App\Utils\CrudHelper;

class FinancialYearForm extends FormAbstract
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
            ->setupModel(new FinancialYear)
            ->setValidatorClass(FinancialYearRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout financial_year'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])
			->add("session_year" , "text", ["label" => "Financial Year", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '2'], "rules" => ""])
			->add("session_start" , "date", ["label" => "Session Start", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-date-format' => 'yyyy-mm-dd','data-date-format' => 'M-yyyy' ],"default_value"=>"","rules" => "required"])
			->add("session_end" , "date", ["label" => "Session End", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-date-format' => 'yyyy-mm-dd','data-date-format' => 'M-yyyy' ],"default_value"=>"","rules" => "required"])
			->add("is_running" , "customRadio", ["label" => "Is Running","label_attr" => ["class" => "control-label required "],"choices"    => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No'),'wrapper' => ['class' => 'form-group col-md-6'], "rules" => "required"])
			->add("description" , "textarea", ["label" => "Description", "label_attr" => ["class" => "control-label  "], "attr"=>["rows" => 4,'data-field_index' => '6'],'wrapper' => ['class' => 'form-group col-md-6'], "rules" => ""])
			->add("is_enabled" , "hidden", ["label" => "Is Enabled", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])            
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'financial_years')  && !\Arr::has($this->model, 'financial_years.0')) ?(object) $this->model['financial_years'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = FinancialYear::getModel();
        }

        $this
            
            ->setupModel(new FinancialYear)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(FinancialYearRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout financial_year'>
                    <fieldset><div class='row'>"])
			
			->add("session_year" , "static", ["tag" => "div" , "label" => "Financial Year" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("session_start" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->session_start), "label" => "Session Start" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("session_end" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->session_end), "label" => "Session End" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("is_running" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->is_running, 'radio', '1:Yes,0:No', $this->model, ''), "label" => "Is Running" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("description" , "static", ["tag" => "div" , "label" => "Description" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
