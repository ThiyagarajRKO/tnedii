<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\QualificationsRequest;
use Impiger\MasterDetail\Models\Qualifications;
use DB;
use App\Utils\CrudHelper;

class QualificationsForm extends FormAbstract
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
            ->setupModel(new Qualifications)
            ->setValidatorClass(QualificationsRequest::class)
            ->withCustomFields()
            ->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("name" , "text", ["label" => "Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-8'],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			->add("department" , "text", ["label" => "Department", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-8'],'attr' => ['data-field_index' => '0'], "rules" => "required"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'qualifications')  && !\Arr::has($this->model, 'qualifications.0')) ?(object) $this->model['qualifications'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = Qualifications::getModel();
        }

        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new Qualifications)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(QualificationsRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            
			->add("name" , "static", ["tag" => "div" , "label" => "Name" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("department" , "static", ["tag" => "div" , "label" => "Department" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
    
}
