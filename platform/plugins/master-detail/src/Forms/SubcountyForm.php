<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\SubcountyRequest;
use Impiger\MasterDetail\Models\Subcounty;
use DB;
use App\Utils\CrudHelper;

class SubcountyForm extends FormAbstract
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
            
            ->setupModel(new Subcounty)
            ->setValidatorClass(SubcountyRequest::class)
            ->withCustomFields()
            ->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("name" , "text", ["label" => "Subcounty Name", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			->add("county_id" , "customSelect", ["label" => "County Name", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'county', 'id', 'name', 'deleted_at IS NULL', '', $this->model, '', $this->getName(), ''),"empty_value" => "Select", "rules" => "required"])
			
            ->setActionButtons(view('plugins/crud::module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {
        
//		$this->model = (\Arr::get($this->model, 'subcounties')  && !\Arr::has($this->model, 'subcounties.0')) ?(object) $this->model['subcounties'] : $this->model;

        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new Subcounty)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(SubcountyRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            
			->add("name" , "static", ["tag" => "div" , "label" => "Subcounty Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("county_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->county_id, 'database', 'county|id|name', $this->model, ''), "label" => "County Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
    
}
