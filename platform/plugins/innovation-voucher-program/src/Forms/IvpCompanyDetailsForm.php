<?php

namespace Impiger\InnovationVoucherProgram\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\InnovationVoucherProgram\Http\Requests\IvpCompanyDetailsRequest;
use Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails;
use DB;
use App\Utils\CrudHelper;


class IvpCompanyDetailsForm extends FormAbstract
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
            ->setupModel(new IvpCompanyDetails)
            ->setValidatorClass(IvpCompanyDetailsRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout ivp_company_details'>
                    <fieldset><legend class='grouppedLegend'> Company Details</legend><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("company_name" , "text", ["label" => "Company Name", "label_attr" => ["class" => "control-label  required"],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '2'], "rules" => ""])
			->add("designation" , "text", ["label" => "Applicant's designation in the company", "label_attr" => ["class" => "control-label  required"],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '3'], "rules" => ""])
			->add("company_address" , "textarea", ["label" => "Company Address", "label_attr" => ["class" => "control-label  required"], "attr"=>["rows" => 4,'data-field_index' => '4'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("company_classification" , "text", ["label" => "Company Classification", "label_attr" => ["class" => "control-label  required"],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '5'], "rules" => ""])
			->add("website" , "text", ["label" => "Website", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '6'], "rules" => ""])
			->add("certificate" , CrudHelper::getFileType("mediaFile"), ["label" => "MSME (UAM) / Startup (Startup India) Registration Certificate", "label_attr" => ["class" => "control-label  required"],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("registration_number" , "text", ["label" => "UAM / Startup Registration Number", "label_attr" => ["class" => "control-label  required"],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '8'], "rules" => ""])
			->add("registration_date" , "date", ["label" => "Date of Registration", "label_attr" => ["class" => "control-label  required"],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => ""])
			->add("annual_turnover" , "text", ["label" => "Annual Turnover ( in Rupees)", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '10'], "rules" => ""])
			->add("no_of_employees" , "text", ["label" => "No Of Employees", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '11'], "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'ivp_company_details')  && !\Arr::has($this->model, 'ivp_company_details.0')) ?(object) $this->model['ivp_company_details'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = IvpCompanyDetails::getModel();
        }
        
        
        $this
            
            ->setupModel(new IvpCompanyDetails)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(IvpCompanyDetailsRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout ivp_company_details'>
                    <fieldset><legend class='grouppedLegend'> Company Details</legend><div class='row'>"])
			
			->add("company_name" , "static", ["tag" => "div" , "label" => "Company Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("designation" , "static", ["tag" => "div" , "label" => "Applicant's designation in the company" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("company_address" , "static", ["tag" => "div" , "label" => "Company Address" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("company_classification" , "static", ["tag" => "div" , "label" => "Company Classification" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("website" , "static", ["tag" => "div" , "label" => "Website" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("certificate" , "static", ["tag" => "a" , "label" => "MSME (UAM) / Startup (Startup India) Registration Certificate" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls' ,'href' =>'/storage/'.$this->model->certificate ,'target'=>'_blank']])
                        ->add("registration_number" , "static", ["tag" => "div" , "label" => "UAM / Startup Registration Number" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("registration_date" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->registration_date), "label" => "Date of Registration" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("annual_turnover" , "static", ["tag" => "div" , "label" => "Annual Turnover ( in Rupees)" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("no_of_employees" , "static", ["tag" => "div" , "label" => "No Of Employees" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
