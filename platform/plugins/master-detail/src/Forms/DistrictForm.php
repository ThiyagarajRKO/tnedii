<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\DistrictRequest;
use Impiger\MasterDetail\Models\District;
use DB;
use App\Utils\CrudHelper;


class DistrictForm extends FormAbstract
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
            
            ->setupModel(new District)
            ->setValidatorClass(DistrictRequest::class)
            ->withCustomFields()
            ->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("name" , "text", ["label" => "District Name", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			->add("code" , "text", ["label" => "Code", "label_attr" => ["class" => "control-label  "],'attr' => ['data-field_index' => '0'], "rules" => ""])
			->add("country_id" , "customSelect", ["label" => "Country Name", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'countries', 'id', 'country_name', 'deleted_at IS NULL', '', $this->model, '', $this->getName(), ''),"empty_value" => "Select", "rules" => "required"])
			->add("region_id" , "customSelect", ["label" => "Region", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'regions', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),"empty_value" => "Select", "rules" => "required"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'districts')  && !\Arr::has($this->model, 'districts.0')) ?(object) $this->model['districts'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = District::getModel();
        }
        
        
        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new District)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(DistrictRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            
			->add("name" , "static", ["tag" => "div" , "label" => "District Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("code" , "static", ["tag" => "div" , "label" => "Code" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("country_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->country_id, 'database', 'countries|id|country_name', $this->model, ''), "label" => "Country Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("region_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->region_id, 'database', 'regions|id|name', $this->model, ''), "label" => "Region" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
    
}
