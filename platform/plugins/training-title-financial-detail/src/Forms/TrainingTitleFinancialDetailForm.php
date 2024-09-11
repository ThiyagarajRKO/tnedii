<?php

namespace Impiger\TrainingTitleFinancialDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\TrainingTitleFinancialDetail\Http\Requests\TrainingTitleFinancialDetailRequest;
use Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail;
use DB;
use App\Utils\CrudHelper;


class TrainingTitleFinancialDetailForm extends FormAbstract
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
            ->setupModel(new TrainingTitleFinancialDetail)
            ->setValidatorClass(TrainingTitleFinancialDetailRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout training_title_financial_detail'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])
			->add("division_id" , "customSelect", ["label" => "Division", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'divisions', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("financial_year_id" , "customSelect", ["label" => "Financial Year", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '3'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'financial_year', 'id', 'session_year', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("annual_action_plan_id" , "customSelect", ["label" => "Training/Workshop/Program Name", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'financial_year_id','data-dd_parentkey' => 'financial_year_id','data-dd_table' => 'annual_action_plan','data-dd_key' => 'id','data-dd_lookup' => 'name' ,'data-field_index' => '4'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'annual_action_plan', 'id', 'name', '', 'financial_year_id', $this->model, 'financial_year_id', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("training_title_id" , "customSelect", ["label" => "Training Name & Code", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'annual_action_plan_id','data-dd_parentkey' => 'annual_action_plan_id','data-dd_table' => 'training_title','data-dd_key' => 'id','data-dd_lookup' => 'code|venue' ,'data-field_index' => '5'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'training_title', 'id', 'code|venue', '', 'annual_action_plan_id', $this->model, 'annual_action_plan_id', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("budget_approved" , "text", ["label" => "Budget Approved", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '6'], "rules" => "required"])
			->add("actual_expenditure" , "text", ["label" => "Actual Expenditure", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '7'], "rules" => "required"])
			->add("edi_admin_cost" , "text", ["label" => "Edi Admin Cost", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '8'], "rules" => "required"])
			->add("revenue_generated" , "text", ["label" => "Revenue Generated", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '9'], "rules" => "required"])
			->add("neft_cheque_no" , "text", ["label" => "Neft Cheque No", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '10'], "rules" => ""])
			->add("neft_cheque_date" , "date", ["label" => "Neft Cheque Date", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'training_title_financial_details')  && !\Arr::has($this->model, 'training_title_financial_details.0')) ?(object) $this->model['training_title_financial_details'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = TrainingTitleFinancialDetail::getModel();
        }
        
        
        $this
            
            ->setupModel(new TrainingTitleFinancialDetail)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(TrainingTitleFinancialDetailRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout training_title_financial_detail'>
                    <fieldset><div class='row'>"])
			
			->add("division_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->division_id, 'database', 'divisions|id|name', $this->model, ''), "label" => "Division" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("financial_year_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->financial_year_id, 'database', 'financial_year|id|session_year', $this->model, ''), "label" => "Financial Year" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("annual_action_plan_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->annual_action_plan_id, 'database', 'annual_action_plan|id|name', $this->model, ''), "label" => "Training/Workshop/Program Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("training_title_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->training_title_id, 'database', 'training_title|id|code', $this->model, ''), "label" => "Training Name & Code" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("budget_approved" , "static", ["tag" => "div" , "label" => "Budget Approved" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("actual_expenditure" , "static", ["tag" => "div" , "label" => "Actual Expenditure" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("edi_admin_cost" , "static", ["tag" => "div" , "label" => "Edi Admin Cost" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("revenue_generated" , "static", ["tag" => "div" , "label" => "Revenue Generated" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("neft_cheque_no" , "static", ["tag" => "div" , "label" => "Neft Cheque No" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("neft_cheque_date" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->neft_cheque_date), "label" => "Neft Cheque Date" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
