<?php

namespace Impiger\SpokeRegistration\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\SpokeRegistration\Http\Requests\SpokeRegistrationRequest;
use Impiger\SpokeRegistration\Models\SpokeRegistration;
use DB;
use App\Utils\CrudHelper;
use Impiger\Workflows\Traits\WorkflowProperty;

class SpokeRegistrationForm extends FormAbstract
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
            ->setupModel(new SpokeRegistration)
            ->setValidatorClass(SpokeRegistrationRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout spoke_registration'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("name" , "text", ["label" => "Name Of Institution", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '2'], "rules" => "required"])
			->add("stream_of_institution" , "customSelect", ["label" => "Stream Of Institution", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '3'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="stream_of_institutions"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("category" , "customSelect", ["label" => "Category", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '4'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="type_of_college"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("affiliation" , "customSelect", ["label" => "Affiliation", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '5'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="university_types"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("hub_institution_id" , "customSelect", ["label" => "Hub", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '6'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'hub_institutions', 'id', 'name|hub_code', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("year_of_establishment" , "text", ["label" => "Year Of Establishment", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '7'], "rules" => "required"])
			->add("locality_type" , "customSelect", ["label" => "Locality Type", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '8'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="locality_type"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("institute_state" , "customSelect", ["label" => "Institute State", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '9'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="coeducation_type"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("program_level" , "customSelect", ["label" => "Program Level", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '10'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="program_level"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("has_incubator" , "customRadio", ["label" => "Has Incubator","label_attr" => ["class" => "control-label  "],"choices"    => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No'),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("address" , "textarea", ["label" => "Address", "label_attr" => ["class" => "control-label required "], "attr"=>["rows" => 4,'data-field_index' => '12'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
			->add("pin_code" , "text", ["label" => "Pin Code", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '13'], "rules" => "required"])
			->add("city" , "text", ["label" => "City", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '14'], "rules" => "required"])
			->add("district_id" , "customSelect", ["label" => "District", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '15'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'district', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])
			->add("phone_no" , "text", ["label" => "Phone No", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '16'], "rules" => "required"])
			->add("email" , "text", ["label" => "Email", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '17'], "rules" => "required"])
			->add("website" , "text", ["label" => "Website", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '18'], "rules" => ""])
			->add("advisory_commitee" , "repeater", ['label'      => __('Advisory Commitee'),
    'label_attr' => ['class' => 'control-label'],
    'fields' => [
        [
            'type'       => 'text',
            'label'      => __('Name of Member'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'member_name',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
        [
            'type'       => 'text',
            'label'      => __('Role/Position'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'role',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Email'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'email',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Phone/Mobile'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'phone',
                'value'   => null,
                'options' => [
                    'data-rules'=> 'numeric|digits:10',
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],        
        
    ],'wrapper' => ['class' => 'form-group col-md-12']
])
			->add("department_faculty_coordinators" , "repeater", [
    'label'      => __('Department Faculty Coordinators'),
    'label_attr' => ['class' => 'control-label'],
    'fields' => [
        [
            'type'       => 'text',
            'label'      => __('Department'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'department',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Name of Faculty'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'faculty_name',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
        [
            'type'       => 'text',
            'label'      => __('Designation'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'designation',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Email'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'email',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Phone/Mobile'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'phone',
                'value'   => null,
                'options' => [
                    'data-rules'=> 'numeric|digits:10',
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],        
        
    ],'wrapper' => ['class' => 'form-group col-md-12']
])
			->add("location_of_e_cell" , "text", ["label" => "Location Of E Cell", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '21'], "rules" => ""])
			->add("availability_space" , "text", ["label" => "Availability Space", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '22'], "rules" => ""])
			->add("internet" , "customRadio", ["label" => "Internet","label_attr" => ["class" => "control-label  "],"choices"    => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No'),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("telephone" , "customRadio", ["label" => "Telephone","label_attr" => ["class" => "control-label  "],"choices"    => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No'),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("budget" , "text", ["label" => "Budget", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '25'], "rules" => ""]);
			if(CrudHelper::isFieldVisible('', '1', 'edit')){
                                    $this->add('body', 'textarea', [
                                        'template' => 'module.users',
                                    ]);
                                }	
                    $this
                        ->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'spoke_registrations')  && !\Arr::has($this->model, 'spoke_registrations.0')) ?(object) $this->model['spoke_registrations'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = SpokeRegistration::getModel();
        }
        
        $isWorkflowSupport = $this->isWorkflowSupport($this->model->getTable());
 $this->setFormOption('isWorkflowSupport', $isWorkflowSupport);
        $this
            
            ->setupModel(new SpokeRegistration)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(SpokeRegistrationRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout spoke_registration'>
                    <fieldset><div class='row'>"])
			
			->add("name" , "static", ["tag" => "div" , "label" => "Name Of Institution" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("stream_of_institution" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->stream_of_institution, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Stream Of Institution" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("category" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->category, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Category" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("affiliation" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->affiliation, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Affiliation" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("hub_institution_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->hub_id, 'database', 'hub_institutions|id|name', $this->model, ''), "label" => "Hub" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("year_of_establishment" , "static", ["tag" => "div" , "label" => "Year Of Establishment" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("locality_type" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->locality_type, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Locality Type" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("institute_state" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->institute_state, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Institute State" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("program_level" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->program_level, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Program Level" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("has_incubator" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->has_incubator, 'radio', '1:Yes,0:No,:No', $this->model, ''), "label" => "Has Incubator" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("address" , "static", ["tag" => "div" , "label" => "Address" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("pin_code" , "static", ["tag" => "div" , "label" => "Pin Code" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("city" , "static", ["tag" => "div" , "label" => "City" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("district_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->district_id, 'database', 'district|id|name', $this->model, ''), "label" => "District" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("phone_no" , "static", ["tag" => "div" , "label" => "Phone No" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("email" , "static", ["tag" => "div" , "label" => "Email" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("website" , "static", ["tag" => "div" , "label" => "Website" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("advisory_commitee" , "repeater", ['label'      => __('Advisory Commitee'),
    'label_attr' => ['class' => 'control-label'],
    'fields' => [
        [
            'type'       => 'text',
            'label'      => __('Name of Member'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'member_name',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
        [
            'type'       => 'text',
            'label'      => __('Role/Position'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'role',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Email'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'email',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Phone/Mobile'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'phone',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],        
        
    ],'wrapper' => ['class' => 'form-group col-md-12']
])
			->add("department_faculty_coordinators" , "repeater", [
    'label'      => __('Department Faculty Coordinators'),
    'label_attr' => ['class' => 'control-label'],
    'fields' => [
        [
            'type'       => 'text',
            'label'      => __('Department'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'department',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Name of Faculty'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'faculty_name',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
        [
            'type'       => 'text',
            'label'      => __('Designation'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'designation',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Email'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'email',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Phone/Mobile'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'phone',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],        
        
    ],'wrapper' => ['class' => 'form-group col-md-12']
])
			->add("location_of_e_cell" , "static", ["tag" => "div" , "label" => "Location Of E Cell" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("availability_space" , "static", ["tag" => "div" , "label" => "Availability Space" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("internet" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->internet, 'radio', '1:Yes,0:No,:No', $this->model, ''), "label" => "Internet" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("telephone" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->telephone, 'radio', '1:Yes,0:No,:No', $this->model, ''), "label" => "Telephone" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("budget" , "static", ["tag" => "div" , "label" => "Budget" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
