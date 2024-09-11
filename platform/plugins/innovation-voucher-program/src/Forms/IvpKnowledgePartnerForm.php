<?php

namespace Impiger\InnovationVoucherProgram\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\InnovationVoucherProgram\Http\Requests\IvpKnowledgePartnerRequest;
use Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner;
use DB;
use App\Utils\CrudHelper;


class IvpKnowledgePartnerForm extends FormAbstract
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
            ->setupModel(new IvpKnowledgePartner)
            ->setValidatorClass(IvpKnowledgePartnerRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout ivp_knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'> Knowledge Partner</legend><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("organization_type" , "customRadio", ["label" => "Type of Organisation","label_attr" => ["class" => "control-label required "],"choices"    => CrudHelper::getRadioOptionValues('datalist', 'National Research Institution:National Research Institution|Institute of Higher Education recognized by MHRD/UGC:Institute of Higher Education recognized by MHRD/UGC|Research lab of MNC or other large Industry:Research lab of MNC or other large Industry|Technology Business Incubation Centers:Technology Business Incubation Centers|Product design consulting firm / Design lab with at least 3 years experience:Product design consulting firm / Design lab with at least 3 years experience'),'wrapper' => ['class' => 'form-group form-group col-md-12'], "rules" => "required"])
			->add("organization_name" , "text", ["label" => "Name of the Organization", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("contact_person" , "text", ["label" => "Name of the Contact person", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '4'], "rules" => "required"])
			->add("designation" , "text", ["label" => "Designation of the Contact person", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '5'], "rules" => "required"])
			->add("mobile_number" , "text", ["label" => "Mobile Number of the Contact person", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '6'], "rules" => "required"])
			->add("email_id" , "text", ["label" => "Email ID of the Contact person", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '7'], "rules" => "required"])
			->add("responsibilities" , "text", ["label" => "What are the precise duties and responsibilities", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '8'], "rules" => "required"])
			->add("attachment" , CrudHelper::getFileType("mediaFile"), ["label" => "Attach Consent of the Knowledge Partner", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'ivp_knowledge_partners')  && !\Arr::has($this->model, 'ivp_knowledge_partners.0')) ?(object) $this->model['ivp_knowledge_partners'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = IvpKnowledgePartner::getModel();
        }
        
        
        $this
            
            ->setupModel(new IvpKnowledgePartner)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(IvpKnowledgePartnerRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout ivp_knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'> Knowledge Partner</legend><div class='row'>"])
			
			->add("organization_type" , "static", ["tag" => "div" , "label" => "Type of Organisation" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("organization_name" , "static", ["tag" => "div" , "label" => "Name of the Organization" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("contact_person" , "static", ["tag" => "div" , "label" => "Name of the Contact person in the Knowledge Partner" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("designation" , "static", ["tag" => "div" , "label" => "Designation of the Contact person" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("mobile_number" , "static", ["tag" => "div" , "label" => "Mobile Number of the Contact person" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("email_id" , "static", ["tag" => "div" , "label" => "Email ID of the Contact person" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("responsibilities" , "static", ["tag" => "div" , "label" => "What are the precise duties and responsibilities of Knowledge Partner" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("attachment" , "static", ["tag" => "a" , "label" => "Attach Consent of the Knowledge Partner" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls' ,'href' =>'/storage/'.$this->model->attachment ,'target'=>'_blank']])
                        ->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
