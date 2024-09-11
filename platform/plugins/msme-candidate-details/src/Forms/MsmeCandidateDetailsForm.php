<?php

namespace Impiger\MsmeCandidateDetails\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MsmeCandidateDetails\Http\Requests\MsmeCandidateDetailsRequest;
use Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails;
use DB;
use App\Utils\CrudHelper;


class MsmeCandidateDetailsForm extends FormAbstract
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
            ->setupModel(new MsmeCandidateDetails)
            ->setValidatorClass(MsmeCandidateDetailsRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout msme_candidate_details'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("scheme" , "customSelect", ["label" => "Scheme", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="msme_scheme"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("candidate_msme_ref_id" , "text", ["label" => "Candidate Msme Ref Id", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("candidate_name" , "text", ["label" => "Candidate Name", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '4'], "rules" => ""])
			->add("care_of" , "customSelect", ["label" => "Care Of", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-field_index' => '5'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="care_of"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => ""])
			->add("father_husband_name" , "text", ["label" => "Father Husband Name", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '6'], "rules" => ""])
			->add("spouse_name" , "text", ["label" => "Spouse Name", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '7'], "rules" => ""])
			->add("gender" , "customSelect", ["label" => "Gender", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-field_index' => '8'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="gender"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => ""])
			->add("category" , "customSelect", ["label" => "Community", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-field_index' => '8'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="community"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => ""])
			->add("mobile_no" , "text", ["label" => "Mobile No", "label_attr" => ["class" => "control-label  required"],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '9'], "rules" => ""])
			->add("email" , "text", ["label" => "Email", "label_attr" => ["class" => "control-label  required"],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '10'], "rules" => ""])
			->add("dob" , "date", ["label" => "Dob", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => ""])
			->add("enroll_start_date" , "date", ["label" => "Enroll Start Date", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => ""])
			->add("qualification" , "text", ["label" => "Qualification", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '12'], "rules" => ""])
			->add("district" , "customSelect", ["label" => "District", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-field_index' => '13'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'district', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => ""])
			->add("address" , "textarea", ["label" => "Address", "label_attr" => ["class" => "control-label  "], "attr"=>["rows" => 4,'data-field_index' => '14'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
//			->add("is_enrolled" , "text", ["label" => "Is Enrolled", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '15'], "rules" => ""])
			->add("photo" , CrudHelper::getFileType("mediaImage"), ["label" => "Photo", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'msme_candidate_details')  && !\Arr::has($this->model, 'msme_candidate_details.0')) ?(object) $this->model['msme_candidate_details'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = MsmeCandidateDetails::getModel();
        }
        $photo = "";
        $iMimeType = array('tif','tiff','webp','svg','png','jpeg','jpg','gif','bmp','avif');
        if($this->model->id){
            $fileExtention = substr($this->model->photo, strrpos($this->model->photo, '.') + 1);
            if(in_array($fileExtention,$iMimeType)){
                $photo="/storage/".$this->model->photo;
            }else{
                $photo = $this->model->photo;
            }
        }
        
        $this
            
            ->setupModel(new MsmeCandidateDetails)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(MsmeCandidateDetailsRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout msme_candidate_details'>
                    <fieldset><div class='row'>"])
			
			->add("scheme" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->scheme, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Scheme" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("candidate_msme_ref_id" , "static", ["tag" => "div" , "label" => "Candidate Msme Ref Id" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("candidate_name" , "static", ["tag" => "div" , "label" => "Candidate Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("care_of" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->care_of, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Care Of" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("father_husband_name" , "static", ["tag" => "div" , "label" => "Father Husband Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("spouse_name" , "static", ["tag" => "div" , "label" => "Spouse Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("gender" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->gender, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Gender" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("category" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->category, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Community" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("mobile_no" , "static", ["tag" => "div" , "label" => "Mobile No" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("email" , "static", ["tag" => "div" , "label" => "Email" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("dob" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->dob), "label" => "Dob" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("qualification" , "static", ["tag" => "div" , "label" => "Qualification" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("district" , "static", ["tag" => "div" ,'value' => CrudHelper::formatRows($this->model->district_id, 'database', 'district|id|name', $this->model, ''), "label" => "District" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("address" , "static", ["tag" => "div" , "label" => "Address" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("is_enrolled" , "static", ["tag" => "div" , "label" => "Is Enrolled" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("photo" , "static", ["tag" => "img" ,"value"=>"", "label" => "Photo" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls preview_image' ,'src' =>$photo]])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
