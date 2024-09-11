<?php

namespace Impiger\Mentee\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Mentee\Http\Requests\MenteeRequest;
use Impiger\Mentee\Models\Mentee;
use DB;
use App\Utils\CrudHelper;


class MenteeForm extends FormAbstract
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
            ->setupModel(new Mentee)
            ->setValidatorClass(MenteeRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout mentee'>
                    <fieldset><div class='row'>"])
            
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])
			// ->add("entrepreneur_id" , "customSelect", ["label" => "Entrepreneur", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices" => CrudHelper::getSelectOptionValues('external', '', 'entrepreneurs', 'id', 'name', 'id NOT IN (SELECT entrepreneur_id FROM mentors)', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("entrepreneur_id" , "customSelect", ["label" => "Entrepreneur", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices" => [],'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("mentor_id" , "customSelect", ["label" => "Mentor", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '3'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'entrepreneurs', 'id', 'name', 'id IN (SELECT entrepreneur_id FROM mentors)', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("industry_id" , "customSelect", ["label" => "Industry", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '4'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="industries"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("specialization_id" , "customSelect", ["label" => "Specialization", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'industry_id','data-dd_parentkey' => 'industry_id','data-dd_table' => 'specializations','data-dd_key' => 'id','data-dd_lookup' => 'name' ,'data-field_index' => '5'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'specializations', 'id', 'name', '', 'industry_id', $this->model, 'industry_id', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("experience_id" , "customSelect", ["label" => "Experience", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '6'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="experiences"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("last_use_id" , "customSelect", ["label" => "Last Use", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '7'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="last_uses"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("proficiency_level_id" , "customSelect", ["label" => "Proficiency Level", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '8'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="proficiency_level"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("qualification_id" , "customSelect", ["label" => "Qualification", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '9'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'qualifications', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("description" , "textarea", ["label" => "Description", "label_attr" => ["class" => "control-label required "], "attr"=>["rows" => 4,'data-field_index' => '10'],'wrapper' => ['class' => 'form-group col-md-6'], "rules" => "required"])
			->add("resume" , CrudHelper::getFileType("mediaFile"), ["label" => "Upload Resume", "label_attr" => ["class" => "control-label required "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-6'], "rules" => "required"])
			->add("status_id" , "customSelect", ["label" => "Status", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '12'],"choices"    => CrudHelper::getSelectOptionValues('datalist', '1:Active|0:Inactive', '', '', '', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'mentees')  && !\Arr::has($this->model, 'mentees.0')) ?(object) $this->model['mentees'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = Mentee::getModel();
        }
        
        
        $this
            
            ->setupModel(new Mentee)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(MenteeRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout mentee'>
                    <fieldset><div class='row'>"])
			
			->add("entrepreneur_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->entrepreneur_id, 'database', 'entrepreneurs|id|name', $this->model, ''), "label" => "Entrepreneur" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("mentor_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->mentor_id, 'database', 'entrepreneurs|id|name', $this->model, ''), "label" => "Mentor" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("industry_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->industry_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Industry" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("specialization_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->specialization_id, 'database', 'specializations|id|name', $this->model, ''), "label" => "Specialization" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("experience_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->experience_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Experience" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("last_use_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->last_use_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Last Use" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("proficiency_level_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->proficiency_level_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Proficiency Level" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("qualification_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->qualification_id, 'database', 'qualifications|id|name', $this->model, ''), "label" => "Qualification" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("description" , "static", ["tag" => "div" , "label" => "Description" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("resume" , "mediaFile", ["label" => "Upload Resume", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("status_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->status_id, 'radio', '1:Active,0:Inactive', $this->model, ''), "label" => "Status" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
