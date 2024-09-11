<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\CountyRequest;
use Impiger\MasterDetail\Models\County;
use DB;
use App\Utils\CrudHelper;

class CountyForm extends FormAbstract
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
            
            ->setupModel(new County)
            ->setValidatorClass(CountyRequest::class)
            ->withCustomFields()
            ->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("name" , "text", ["label" => "County Name", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			->add("district_id" , "customSelect", ["label" => "District Name", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'district', 'id', 'name', 'deleted_at IS NULL', '', $this->model, '', $this->getName(), ''),"empty_value" => "Select", "rules" => "required"])
			
            ->setActionButtons(view('plugins/crud::module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {
        
//		$this->model = (\Arr::get($this->model, 'counties')  && !\Arr::has($this->model, 'counties.0')) ?(object) $this->model['counties'] : $this->model;

        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new County)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(CountyRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            
			->add("name" , "static", ["tag" => "div" , "label" => "County Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("district_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->district_id, 'database', 'district|id|name', $this->model, ''), "label" => "District Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
    
}
