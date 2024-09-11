<?php

namespace Impiger\AnnualActionPlan\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\AnnualActionPlan\Http\Requests\AnnualActionPlanRequest;
use Impiger\AnnualActionPlan\Models\AnnualActionPlan;
use DB;
use App\Utils\CrudHelper;


class AnnualActionPlanForm extends FormAbstract
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
            ->setupModel(new AnnualActionPlan)
            ->setValidatorClass(AnnualActionPlanRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout annual_action_plan'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])
			->add("name" , "text", ["label" => "Training/Workshop/Program Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '2'], "rules" => "required"])
			->add("financial_year_id" , "customSelect", ["label" => "Financial Year", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '3'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'financial_year', 'id', 'session_year', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("division_id" , "customSelect", ["label" => "Division", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '4'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'divisions', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("officer_incharge_designation_id" , "customSelect", ["label" => "Officer Incharge Designation", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '5'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'officer_incharge_designations', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("duration" , "text", ["label" => "Duration", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '6'], "rules" => "required"])
			->add("no_of_batches" , "text", ["label" => "No Of Batches", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '7'], "rules" => "required"])
			->add("budget_per_program" , "text", ["label" => "Budget Per Program", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '8'], "rules" => "required"])
			->add("total_budget" , "text", ["label" => "Total Budget", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '9'], "rules" => "required"])
			->add("batch_size" , "text", ["label" => "Batch Size", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '10'], "rules" => "required"])
			->add("training_module" , "customSelect", ["label" => "Training Type", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '11'],"choices"    => CrudHelper::getSelectOptionValues('datalist', '1:Online|0:Offline', '', '', '', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("remarks" , "textarea", ["label" => "Remarks", "label_attr" => ["class" => "control-label  "], "attr"=>["rows" => 4,'data-field_index' => '16'],'wrapper' => ['class' => 'form-group col-md-6'], "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'annual_action_plans')  && !\Arr::has($this->model, 'annual_action_plans.0')) ?(object) $this->model['annual_action_plans'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = AnnualActionPlan::getModel();
        }
        
        
        $this
            
            ->setupModel(new AnnualActionPlan)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(AnnualActionPlanRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout annual_action_plan'>
                    <fieldset><div class='row'>"])
			
			->add("name" , "static", ["tag" => "div" , "label" => "Training/Workshop/Program Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("financial_year_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->financial_year_id, 'database', 'financial_year|id|session_year', $this->model, ''), "label" => "Financial Year" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("division_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->division_id, 'database', 'divisions|id|name', $this->model, ''), "label" => "Division" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("officer_incharge_designation_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->officer_incharge_designation_id, 'database', 'officer_incharge_designations|id|name', $this->model, ''), "label" => "Officer Incharge Designation" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("duration" , "static", ["tag" => "div" , "label" => "Duration" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("no_of_batches" , "static", ["tag" => "div" , "label" => "No Of Batches" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("budget_per_program" , "static", ["tag" => "div" , "label" => "Budget Per Program" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("total_budget" , "static", ["tag" => "div" , "label" => "Total Budget" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("batch_size" , "static", ["tag" => "div" , "label" => "Batch Size" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("training_module" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->training_module, 'radio', '1:Online,0:OffLine', $this->model, ''), "label" => "Training Type" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("remarks" , "static", ["tag" => "div" , "label" => "Remarks" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
