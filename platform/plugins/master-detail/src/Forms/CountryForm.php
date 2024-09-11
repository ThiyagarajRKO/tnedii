<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\CountryRequest;
use Impiger\MasterDetail\Models\Country;
use DB;
use App\Utils\CrudHelper;

class CountryForm extends FormAbstract
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
            
            ->setupModel(new Country)
            ->setValidatorClass(CountryRequest::class)
            ->withCustomFields()
            ->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("nationality" , "text", ["label" => "Nationality", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			->add("country_code" , "text", ["label" => "Country Code", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			->add("country_name" , "text", ["label" => "Country Name", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			->add("phone_code" , "text", ["label" => "Phone Code", "label_attr" => ["class" => "control-label required "],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			
            ->setActionButtons(view('plugins/crud::module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {
        
//		$this->model = (\Arr::get($this->model, 'countries')  && !\Arr::has($this->model, 'countries.0')) ?(object) $this->model['countries'] : $this->model;

        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new Country)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(CountryRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            
			->add("nationality" , "static", ["tag" => "div" , "label" => "Nationality" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("country_code" , "static", ["tag" => "div" , "label" => "Country Code" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("country_name" , "static", ["tag" => "div" , "label" => "Country Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("phone_code" , "static", ["tag" => "div" , "label" => "Phone Code" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
    
}
