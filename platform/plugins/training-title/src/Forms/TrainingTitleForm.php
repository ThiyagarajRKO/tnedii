<?php

namespace Impiger\TrainingTitle\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\TrainingTitle\Http\Requests\TrainingTitleRequest;
use Impiger\TrainingTitle\Models\TrainingTitle;
use DB;
use App\Utils\CrudHelper;


class TrainingTitleForm extends FormAbstract
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
            ->setupModel(new TrainingTitle)
            ->setValidatorClass(TrainingTitleRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout training_title'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("division_id" , "customSelect", ["label" => "Division", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'divisions', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("financial_year_id" , "customSelect", ["label" => "Year", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '3'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'financial_year', 'id', 'session_year', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("annual_action_plan_id" , "customSelect", ["label" => "Training/Workshop/Program Name", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'division_id','data-dd_parentkey' => 'division_id','data-dd_table' => 'annual_action_plan','data-dd_key' => 'id','data-dd_lookup' => 'name' ,'data-field_index' => '4'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'annual_action_plan', 'id', 'name', '`no_of_batches` > (SELECT COUNT(`annual_action_plan_id`) FROM `training_title` WHERE `annual_action_plan_id` = `annual_action_plan`.`id`)', 'division_id', $this->model, 'division_id', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("code" , "text", ["label" => "Code", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '5'], "rules" => "required"])
			->add("venue" , "text", ["label" => "Venue", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '6'], "rules" => "required"])
			->add("email" , "text", ["label" => "Email", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '7'], "rules" => ""])
			->add("phone" , "text", ["label" => "Phone", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '8'], "rules" => "required"])
			->add("vendor_id" , "customSelect", ["label" => "PIA", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '9'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'vendors', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("officer_incharge_designation_id" , "customSelect", ["label" => "EDII Coordinator", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '10'],"choices"    => CrudHelper::getSelectOptionValues('datalist', '1:Joint Director-EDP|2:Deputy Director-ICT', '', '', '', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("fee_paid" , "customSelect", ["label" => "Fee Paid", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '11'],"choices"    => CrudHelper::getSelectOptionValues('datalist', '1:Free|2:Paid', '', '', '', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("fee_amount" , "text", ["label" => "Fee Amount", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '12'], "rules" => "required"])
			->add("private_workshop" , "customRadio", ["label" => "Private Workshop","label_attr" => ["class" => "control-label required "],"choices"    => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No'),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
			->add("training_start_date" , "date", ["label" => "Training Start Date", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => "required"])
			->add("training_end_date" , "date", ["label" => "Training End Date", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => "required"])
			->add("webinar_link" , "text", ["label" => "Webinar Link", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '16'], "rules" => ""])
			->add("small_content" , "textarea", ["label" => "Small Content", "label_attr" => ["class" => "control-label  "], "attr"=>["rows" => 4,'data-field_index' => '17'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("description" , "textarea", ["label" => "Description", "label_attr" => ["class" => "control-label  "], "attr"=>["rows" => 4,'data-field_index' => '18'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("footer_note" , "text", ["label" => "Footer Note", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '19'], "rules" => ""])
			->add("left_signature" , "text", ["label" => "Left Signature", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '20'], "rules" => ""])
			->add("left_signature_name" , "text", ["label" => "Left Signature Name", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '21'], "rules" => ""])
			->add("left_signature_file" , "text", ["label" => "Left Signature File", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '22'], "rules" => ""])
			->add("middle_signature" , "text", ["label" => "Middle Signature", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '23'], "rules" => ""])
			->add("middle_signature_name" , "text", ["label" => "Middle Signature Name", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '24'], "rules" => ""])
			->add("middle_signature_file" , "text", ["label" => "Middle Signature File", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '25'], "rules" => ""])
			->add("right_signature" , "text", ["label" => "Right Signature", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '26'], "rules" => ""])
			->add("right_signature_name" , "text", ["label" => "Right Signature Name", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '27'], "rules" => ""])
			->add("right_signature_file" , "text", ["label" => "Right Signature File", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '28'], "rules" => ""])
			->add("training_background_image_name" , CrudHelper::getFileType("mediaImage"), ["label" => "Training Background Image", "label_attr" => ["class" => "control-label "], rv_media_handle_upload(request()->file("file"), 0, ""), 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '29'], "rules" => ""])
			->add("training_gallery_url_en" , "textarea", ["label" => "Training Gallery URL (English)", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '30'], "rules" => ""])
			->add("training_gallery_url_ta" , "textarea", ["label" => "Training Gallery URL (Tamil)", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '31'], "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'training_titles')  && !\Arr::has($this->model, 'training_titles.0')) ?(object) $this->model['training_titles'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = TrainingTitle::getModel();
        }
        
        
        $this
            
            ->setupModel(new TrainingTitle)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(TrainingTitleRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout training_title'>
                    <fieldset><div class='row'>"])
			
			->add("division_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->division_id, 'database', 'divisions|id|name', $this->model, ''), "label" => "Division" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("financial_year_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->financial_year_id, 'database', 'financial_year|id|session_year', $this->model, ''), "label" => "Year" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("annual_action_plan_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->annual_action_plan_id, 'database', 'annual_action_plan|id|name', $this->model, ''), "label" => "Training/Workshop/Program Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("code" , "static", ["tag" => "div" , "label" => "Code" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("venue" , "static", ["tag" => "div" , "label" => "Venue" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("email" , "static", ["tag" => "div" , "label" => "Email" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("phone" , "static", ["tag" => "div" , "label" => "Phone" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("vendor_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->vendor_id, 'database', 'vendors|id|name', $this->model, ''), "label" => "PIA" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("officer_incharge_designation_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->officer_incharge_designation_id, 'database', 'officer_incharge_designations|id|name', $this->model, ''), "label" => "EDII Coordinator" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("fee_paid" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->fee_paid, 'radio', '1:Free,2:Paid', $this->model, ''), "label" => "Fee Paid" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("fee_amount" , "static", ["tag" => "div" , "label" => "Fee Amount" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("private_workshop" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->private_workshop, 'radio', '0:No,1:Yes', $this->model, ''), "label" => "Private Workshop" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("training_start_date" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->training_start_date, 'date', '', $this->model, ''), "label" => "Training Start Date" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("training_end_date" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->training_end_date, 'date', '', $this->model, ''), "label" => "Training End Date" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("webinar_link" , "static", ["tag" => "div" , "label" => "Webinar Link" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("small_content" , "static", ["tag" => "div" , "label" => "Small Content" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("description" , "static", ["tag" => "div" , "label" => "Description" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("footer_note" , "static", ["tag" => "div" , "label" => "Footer Note" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("left_signature" , "static", ["tag" => "div" , "label" => "Left Signature" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("left_signature_name" , "static", ["tag" => "div" , "label" => "Left Signature Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("left_signature_file" , "static", ["tag" => "div" , "label" => "Left Signature File" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("middle_signature" , "static", ["tag" => "div" , "label" => "Middle Signature" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("middle_signature_name" , "static", ["tag" => "div" , "label" => "Middle Signature Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("middle_signature_file" , "static", ["tag" => "div" , "label" => "Middle Signature File" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("right_signature" , "static", ["tag" => "div" , "label" => "Right Signature" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("right_signature_name" , "static", ["tag" => "div" , "label" => "Right Signature Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("right_signature_file" , "static", ["tag" => "div" , "label" => "Right Signature File" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("training_background_image_name" , CrudHelper::getFileType("mediaImage"), ["tag" => "div" , "label" => "Training Background Image" , "label_attr" => ["class" => "control-label "], rv_media_handle_upload(request()->file("file"), 0, ""), 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
			->add("training_gallery_url_en" , "static", ["tag" => "div" , "label" => "Training Gallery URL (English)" , "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
			->add("training_gallery_url_ta" , "static", ["tag" => "div" , "label" => "Training Gallery URL (Tamil)" , "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
