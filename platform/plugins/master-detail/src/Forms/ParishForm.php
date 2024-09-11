<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\ParishRequest;
use Impiger\MasterDetail\Models\Parish;
use DB;
use App\Utils\CrudHelper;

class ParishForm extends FormAbstract
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
            
            ->setupModel(new Parish)
            ->setValidatorClass(ParishRequest::class)
            ->withCustomFields()
            ->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("name" , "text", ["label" => "Parish Name", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			->add("sub_county_id" , "customSelect", ["label" => "Subcounty  Name", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'subcounty', 'id', 'name', 'deleted_at IS NULL', '', $this->model, '', $this->getName(), ''),"empty_value" => "Select", "rules" => "required"])
			
            ->setActionButtons(view('plugins/crud::module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {
        
//		$this->model = (\Arr::get($this->model, 'parishes')  && !\Arr::has($this->model, 'parishes.0')) ?(object) $this->model['parishes'] : $this->model;

        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new Parish)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(ParishRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            
			->add("name" , "static", ["tag" => "div" , "label" => "Parish Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("sub_county_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->sub_county_id, 'database', 'subcounty|id|name', $this->model, ''), "label" => "Subcounty  Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
    
}
