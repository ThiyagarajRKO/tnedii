<?php

namespace Impiger\Entrepreneur\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Entrepreneur\Http\Requests\EntrepreneurRequest;
use Impiger\Entrepreneur\Models\Entrepreneur;
use DB;
use App\Utils\CrudHelper;


class EntrepreneurForm extends FormAbstract
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
            ->setupModel(new Entrepreneur)
            ->setValidatorClass(EntrepreneurRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout entrepreneur'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("candidate_type_id" , "customSelect", ["label" => "Candidate Type", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="candidate_type"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("email" , "text", ["label" => "Email", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("password" , "password", ["label" => "Password", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '4'], "rules" => "required"])
			->add("prefix_id" , "customSelect", ["label" => "Prefix", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '5'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="prefixes"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("name" , "text", ["label" => "Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '6'], "rules" => "required"])
			->add("care_of" , "customSelect", ["label" => "Care Of", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '7'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="care_of"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("father_name" , "text", ["label" => "Father/Mother/Husband Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '8'], "rules" => "required"])
			->add("gender_id" , "customSelect", ["label" => "Gender", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '9'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="gender"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("dob" , "date", ["label" => "Dob", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '10'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ], "default_value"=>"", "rules" => "required"])
			->add("aadhaar_no" , "text", ["label" => "Aadhaar No", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '11'], "rules" => ""])
			->add("mobile" , "text", ["label" => "Mobile", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '12'], "rules" => "required"])
			->add("physically_challenged" , "customSelect", ["label" => "Physically Challenged", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '13'],"choices"    => CrudHelper::getSelectOptionValues('datalist', '1:Yes|0:No', '', '', '', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("address" , "textarea", ["label" => "Commercial/Residential Address", "label_attr" => ["class" => "control-label  "], "attr"=>["rows" => 4,'data-field_index' => '14'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("district_id" , "customSelect", ["label" => "District", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '15'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'district', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("pincode" , "text", ["label" => "Pincode", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '16'], "rules" => "required"])
			->add("religion_id" , "customSelect", ["label" => "Religion", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '17'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="religion"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("community" , "customSelect", ["label" => "Community", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '18'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="community"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("student_type_id" , "customSelect", ["label" => "Student Type", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '19'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="student_type"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("student_school_name" , "text", ["label" => "Student School Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '20'], "rules" => "required"])
			->add("student_standard_name" , "text", ["label" => "Student Standard Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '21'], "rules" => "required"])
			->add("student_college_name" , "text", ["label" => "Student College Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '22'], "rules" => "required"])
			->add("student_course_name" , "text", ["label" => "Student Course Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '23'], "rules" => "required"])
			->add("hub_institution_id" , "customSelect", ["label" => "Hub Institution", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '24'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'hub_institutions', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("student_year" , "customSelect", ["label" => "Student Year", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '25'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="course_year"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("spoke_registration_id" , "customSelect", ["label" => "Spoke College Name", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'hub_institution_id','data-dd_parentkey' => 'hub_institution_id','data-dd_table' => 'spoke_registration','data-dd_key' => 'id','data-dd_lookup' => 'name' ,'data-field_index' => '26'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'spoke_registration', 'id', 'name', '', 'hub_institution_id', $this->model, 'hub_institution_id', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("qualification_id" , "customSelect", ["label" => "Qualification", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '27'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'qualifications', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("entrepreneurial_category_id" , "customSelect", ["label" => "Entrepreneurial Category", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-field_index' => '28'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="entrepreneurial_category"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => ""])
			->add("activity_name" , "text", ["label" => "Activity Name", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '29'], "rules" => ""])
			->add("photo_path" , CrudHelper::getFileType("mediaImage"), ["label" => "Photo Path", "label_attr" => ["class" => "control-label required "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'entrepreneurs')  && !\Arr::has($this->model, 'entrepreneurs.0')) ?(object) $this->model['entrepreneurs'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = Entrepreneur::getModel();
        }
        
        
        $this
            
            ->setupModel(new Entrepreneur)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(EntrepreneurRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout entrepreneur'>
                    <fieldset><div class='row'>"])
			
			->add("candidate_type_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->candidate_type_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Candidate Type" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("email" , "static", ["tag" => "div" , "label" => "Email" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("password" , "static", ["tag" => "div" , "label" => "Password" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("prefix_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->prefix_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Prefix" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("name" , "static", ["tag" => "div" , "label" => "Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("care_of" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->care_of, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Care Of" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("father_name" , "static", ["tag" => "div" , "label" => "Father/Mother/Husband Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("gender_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->gender_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Gender" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("dob" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->dob), "label" => "Dob" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("aadhaar_no" , "static", ["tag" => "div" , "label" => "Aadhaar No" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("mobile" , "static", ["tag" => "div" , "label" => "Mobile" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("physically_challenged" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->physically_challenged, 'radio', '0:No,1:Yes', $this->model, ''), "label" => "Physically Challenged" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("address" , "static", ["tag" => "div" , "label" => "Commercial/Residential Address" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("district_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->district_id, 'database', '', $this->model, '1:district:id:name'), "label" => "District" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("pincode" , "static", ["tag" => "div" , "label" => "Pincode" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("religion_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->religion_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Religion" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("community" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->community, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Community" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("student_type_id" , "static", ["tag" => "div" , "label" => "Student Type" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("student_school_name" , "static", ["tag" => "div" , "label" => "Student School Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("student_standard_name" , "static", ["tag" => "div" , "label" => "Student Standard Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("student_college_name" , "static", ["tag" => "div" , "label" => "Student College Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("student_course_name" , "static", ["tag" => "div" , "label" => "Student Course Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("hub_institution_id" , "static", ["tag" => "div" , "label" => "Hub Institution" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("student_year" , "static", ["tag" => "div" , "label" => "Student Year" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("spoke_registration_id" , "static", ["tag" => "div" , "label" => "Spoke College Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("qualification_id" , "static", ["tag" => "div" , "label" => "Qualification" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("entrepreneurial_category_id" , "static", ["tag" => "div" , "label" => "Entrepreneurial Category" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("activity_name" , "static", ["tag" => "div" , "label" => "Activity Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("photo_path" , "mediaImage", ["label" => "Photo Path", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
