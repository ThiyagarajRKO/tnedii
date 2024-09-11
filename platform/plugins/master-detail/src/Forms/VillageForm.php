<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\VillageRequest;
use Impiger\MasterDetail\Models\Village;
use DB;
use App\Utils\CrudHelper;

class VillageForm extends FormAbstract
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
            
            ->setupModel(new Village)
            ->setValidatorClass(VillageRequest::class)
            ->withCustomFields()
            ->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("name" , "text", ["label" => "Village Name", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			->add("parish_id" , "customSelect", ["label" => "Parish Name", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'parish', 'id', 'name', 'deleted_at IS NULL', '', $this->model, '', $this->getName(), ''),"empty_value" => "Select", "rules" => "required"])
			
            ->setActionButtons(view('plugins/crud::module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {
        
//		$this->model = (\Arr::get($this->model, 'villages')  && !\Arr::has($this->model, 'villages.0')) ?(object) $this->model['villages'] : $this->model;

        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new Village)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(VillageRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            
			->add("name" , "static", ["tag" => "div" , "label" => "Village Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("parish_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->parish_id, 'database', 'parish|id|name', $this->model, ''), "label" => "Parish Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
    
}
