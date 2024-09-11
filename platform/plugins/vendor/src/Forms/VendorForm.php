<?php

namespace Impiger\Vendor\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Vendor\Http\Requests\VendorRequest;
use Impiger\Vendor\Models\Vendor;
use DB;
use App\Utils\CrudHelper;


class VendorForm extends FormAbstract
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
            ->setupModel(new Vendor)
            ->setValidatorClass(VendorRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout vendor'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("user_id" , "hidden", ["label" => "User", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("name" , "text", ["label" => "Name of the organization", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("pia_constitution_id" , "customSelect", ["label" => "Pia Constitution", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '4'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="pia_constitutions"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("pia_mainactivity_id" , "customSelect", ["label" => "Pia Main Activity", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '5'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="pia_mainactivities"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("email" , "text", ["label" => "Email", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '6'], "rules" => "required"])
			->add("password" , "text", ["label" => "Password", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '7'], "rules" => "required"])
			->add("contact_number" , "text", ["label" => "Mobile no", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '8'], "rules" => "required"])
			->add("phone" , "text", ["label" => "Land Line", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '9'], "rules" => ""])
			->add("address" , "text", ["label" => "Address", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '10'], "rules" => "required"])
			->add("district_id" , "customSelect", ["label" => "District", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '11'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'district', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("pincode" , "text", ["label" => "Pincode", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '12'], "rules" => "required"])
			->add("date_of_establishment" , "date", ["label" => "Date Of Establishment", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => "required"])
			->add("name_principal" , "text", ["label" => "Name and designation of Principal officer", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '14'], "rules" => "required"])
			->add("seating_capacity" , "text", ["label" => "No of Class rooms/Seating capacity", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '15'], "rules" => "required"])
			->add("audio_video" , "text", ["label" => "Audio Video", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '16'], "rules" => "required"])
			->add("rest_dining" , "text", ["label" => "Rest room and dining facilities", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '17'], "rules" => "required"])
			->add("access_distance" , "text", ["label" => "Access Distance", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '18'], "rules" => "required"])
			->add("accommodation_facility" , "text", ["label" => "Accommodation Facility", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '19'], "rules" => "required"])
			->add("refreshment_provision" , "text", ["label" => "Refreshment Provision", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '20'], "rules" => "required"])
			->add("experience_year" , "text", ["label" => "Experience Year", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '21'], "rules" => "required"])
			->add("achivements" , "textarea", ["label" => "Achivements", "label_attr" => ["class" => "control-label required "], "attr"=>["rows" => 4,'data-field_index' => '22'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
			->add("profile" , CrudHelper::getFileType("mediaFile"), ["label" => "Upload detailed profile", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'vendors')  && !\Arr::has($this->model, 'vendors.0')) ?(object) $this->model['vendors'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = Vendor::getModel();
        }
        
        
        $this
            
            ->setupModel(new Vendor)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(VendorRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout vendor'>
                    <fieldset><div class='row'>"])
			
			
			->add("name" , "static", ["tag" => "div" , "label" => "Name of the organization" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("pia_constitution_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->pia_constitution_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Pia Constitution" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("pia_mainactivity_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->pia_mainactivity_id, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Pia Main Activity" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("email" , "static", ["tag" => "div" , "label" => "Email" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("password" , "static", ["tag" => "div" , "label" => "Password" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("contact_number" , "static", ["tag" => "div" , "label" => "Mobile no" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("phone" , "static", ["tag" => "div" , "label" => "Land Line" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("address" , "static", ["tag" => "div" , "label" => "Address" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("district_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->district_id, 'database', 'district|id|name', $this->model, ''), "label" => "District" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("pincode" , "static", ["tag" => "div" , "label" => "Pincode" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("date_of_establishment" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->date_of_establishment), "label" => "Date Of Establishment" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("name_principal" , "static", ["tag" => "div" , "label" => "Name and designation of Principal officer" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("seating_capacity" , "static", ["tag" => "div" , "label" => "No of Class rooms/Seating capacity" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("audio_video" , "static", ["tag" => "div" , "label" => "Audio Video" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("rest_dining" , "static", ["tag" => "div" , "label" => "Rest room and dining facilities" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("access_distance" , "static", ["tag" => "div" , "label" => "Access Distance" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("accommodation_facility" , "static", ["tag" => "div" , "label" => "Accommodation Facility" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("refreshment_provision" , "static", ["tag" => "div" , "label" => "Refreshment Provision" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("experience_year" , "static", ["tag" => "div" , "label" => "Experience Year" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("achivements" , "static", ["tag" => "div" , "label" => "Achivements" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("profile" , "mediaFile", ["label" => "Upload detailed profile", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
