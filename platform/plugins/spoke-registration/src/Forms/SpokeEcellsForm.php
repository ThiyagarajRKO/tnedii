<?php

namespace Impiger\SpokeRegistration\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\SpokeRegistration\Http\Requests\SpokeEcellsRequest;
use Impiger\SpokeRegistration\Models\SpokeEcells;
use DB;
use App\Utils\CrudHelper;
use Impiger\Workflows\Traits\WorkflowProperty;

class SpokeEcellsForm extends FormAbstract
{
    
    use WorkflowProperty;
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
            ->setupModel(new SpokeEcells)
            ->setValidatorClass(SpokeEcellsRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout spoke_ecells'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("spoke_registration_id" , "customSelect", ["label" => "Spoke Institution", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'spoke_registration', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("name" , "text", ["label" => "E-cell Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("logo" , CrudHelper::getFileType("mediaImage"), ["label" => "Logo", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("description" , "textarea", ["label" => "Description", "label_attr" => ["class" => "control-label  "], "attr"=>["rows" => 4,'data-field_index' => '5'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'spoke_ecells')  && !\Arr::has($this->model, 'spoke_ecells.0')) ?(object) $this->model['spoke_ecells'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = SpokeEcells::getModel();
        }
        
        $isWorkflowSupport = $this->isWorkflowSupport($this->model->getTable());
 $this->setFormOption('isWorkflowSupport', $isWorkflowSupport); 
        $this
            
            ->setupModel(new SpokeEcells)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(SpokeEcellsRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout spoke_ecells'>
                    <fieldset><div class='row'>"])
			
			->add("spoke_registration_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->spoke_registration_id, 'database', 'spoke_registration|id|name', $this->model, ''), "label" => "Spoke Institution" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("name" , "static", ["tag" => "div" , "label" => "E-cell Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("logo" , "mediaImage", ["label" => "Logo", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("description" , "static", ["tag" => "div" , "label" => "Description" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
