<?php

namespace Impiger\TnsiStartup\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\TnsiStartup\Http\Requests\TnsiStartupRequest;
use Impiger\TnsiStartup\Models\TnsiStartup;
use DB;
use App\Utils\CrudHelper;
use Impiger\Workflows\Traits\WorkflowProperty;

class TnsiStartupForm extends FormAbstract
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
            ->setupModel(new TnsiStartup)
            ->setValidatorClass(TnsiStartupRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout tnsi_startup'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("region_id" , "customSelect", ["label" => "Region", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'regions', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group form-group col-md-3'],"empty_value" => "Select", "rules" => "required"])
			->add("hub_institution_id" , "customSelect", ["label" => "Hub Institutions", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'hub_institutions', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group form-group col-md-3'],"empty_value" => "Select", "rules" => "required"])
//			->add("college_name" , "customSelect", ["label" => "Name of the College", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'spoke_registration', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("spoke_registration_id" , "customSelect", ["label" => "Name of the College", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'hub_institution_id','data-dd_parentkey' => 'hub_institution_id','data-dd_table' => 'spoke_registration','data-dd_key' => 'id','data-dd_lookup' => 'name' ,'data-field_index' => '26'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'spoke_registration', 'id', 'name', '', 'hub_institution_id', $this->model, 'hub_institution_id', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-3'],"empty_value" => "Select", "rules" => "required"])
                        ->add("name" , "text", ["label" => "Name of your Startup", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group form-group col-md-3'],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("team_members" , "repeater", [
    'label'      => __('Details of Team Members'),
    'label_attr' => ['class' => 'control-label'],
    'fields' => [
        [
            'type'       => 'text',
            'label'      => __('Email Id'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'email_id',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control', 
                                    
                ],
                
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Mobile Number'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'mobile_number',
                'value'   => null,
                'options' => [
                    'data-rules'=> 'numeric|digits:10',
                    'class'        => 'form-control',
                     
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
        [
            'type'       => 'text',
            'label'      => __('Name of the college'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'spoke_registration_id',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',
                     
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Lead Name'),
            'label_attr' => ['class' => 'control-label required lead-label'],
            'attributes' => [
                'name'    => 'name',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control', 
                     
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Date of Birth'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'dob',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ], 
		[
            'type'       => 'text',
            'label'      => __('Aadhaar Number'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'aadhar_number',
                'value'   => null,
                'options' => [
                    'data-rules'=> 'numeric|digits:12',
                    'class'        => 'form-control',
                     
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('District'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'district',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ], [
            'type'       => 'text',
            'label'      => __('Degree & Branch'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'degree',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ], [
            'type'       => 'text',
            'label'      => __('Year'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'year',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],        
       /* [
            'type'       => 'text',
            'label'      => __('Member Type'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'member_type',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control', 
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],*/
    ],'wrapper' => ['class' => 'form-group col-md-12 team_member_repeater']
])
			//->add("idea_about" , "text", ["label" => "Idea is about", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '5'], "rules" => "required"])
            ->add("idea_about" , "customSelect", ["label" => "Idea is about", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '4'],"choices" => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="tnsi_ideas"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])		
            ->add("is_your_idea" , "customSelect", ["label" => "Is Your Idea (Sector)", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '4'],"choices" => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="tnsi_sectors"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => "required"])		
            //->add("is_your_idea" , "text", ["label" => "Is Your Idea", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '6'], "rules" => "required"])
			->add("about_startup" , "text", ["label" => "One Line About Your Startup", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '7'], "rules" => "required"])
			->add("problem_of_address" , "text", ["label" => "What is the problem that you are going to address?", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '8'], "rules" => "required"])
			->add("solution_of_problem" , "text", ["label" => "What are the solutions for the problem addressed?", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '9'], "rules" => "required"])
			->add("unique_selling_proposition" , "text", ["label" => "What's your startup's Unique Selling Proposition (USP)?", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '10'], "rules" => "required"])
			->add("revenue_stream" , "text", ["label" => "How does your startup generate revenue (Write in your own words)?", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '11'], "rules" => "required"])
			->add("description" , "textarea", ["label" => "Detailed description about your Idea", "label_attr" => ["class" => "control-label required "], "attr"=>["rows" => 4,'data-field_index' => '12'],'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
			->add("duration" , "text", ["label" => "How long have you been working on it?", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '13'], "rules" => "required"])
			->add("is_won" , "customRadio", ["label" => "Won any seed grant/prize money with the same idea?","label_attr" => ["class" => "control-label  "],"choices"    => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No'),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("pitch_training" , "customRadio", ["label" => "Have you previously taken part in any pitch training?","label_attr" => ["class" => "control-label  "],"choices"    => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No'),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("is_incubated" , "customRadio", ["label" => "Whether your startup is incubated in any Technology Business Incubator?","label_attr" => ["class" => "control-label  "],"choices"    => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No'),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("demo_link" , "text", ["label" => "Do you have any website / application link / demo that you would like to share?", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '17'], "rules" => ""])
			->add("about_tnsi" , "text", ["label" => "Where did you hear about TNSI?", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '18'], "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'tnsi_startups')  && !\Arr::has($this->model, 'tnsi_startups.0')) ?(object) $this->model['tnsi_startups'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = TnsiStartup::getModel();
        }
        
        $isWorkflowSupport = $this->isWorkflowSupport($this->model->getTable());
 $this->setFormOption('isWorkflowSupport', $isWorkflowSupport);
        $this
            
            ->setupModel(new TnsiStartup)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(TnsiStartupRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout tnsi_startup'>
                    <fieldset><div class='row'>"])
			
			->add("spoke_registration_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->spoke_registration_id, 'database', 'spoke_registration|id|name', $this->model, ''), "label" => "Name of the College" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("name" , "static", ["tag" => "div" , "label" => "Name of your Startup" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("district" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->join_fields()->district_id, 'database', 'district|id|name', $this->model, ''), "label" => "District" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("region" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->join_fields()->region_id, 'database', 'regions|id|name', $this->model, ''), "label" => "Region" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("team_members" , "repeater", [
    'label'      => __('Details of Team Members'),
    'label_attr' => ['class' => 'control-label'],
    'fields' => [
        [
            'type'       => 'text',
            'label'      => __('Email Id'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'email_id',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Mobile Number'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'mobile_number',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
        [
            'type'       => 'text',
            'label'      => __('Name of the college'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'spoke_registration_id',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Lead Name'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'name',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Date of Birth'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'dob',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      =>  __('Aadhaar Number'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'aadhar_number',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ], 
		[
            'type'       => 'text',
            'label'      => __('District'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'district',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],
		[
            'type'       => 'text',
            'label'      => __('Degree & Branch'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'degree',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ], [
            'type'       => 'text',
            'label'      => __('Year'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'year',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ], 
		/*[
            'type'       => 'text',
            'label'      => __('Member Type'),
            'label_attr' => ['class' => 'control-label required'],
            'attributes' => [
                'name'    => 'member_type',
                'value'   => null,
                'options' => [
                    'class'        => 'form-control',                    
                ],
            ],
			'wrapper' => ['class' => 'form-group col-md-3']
        ],*/        
        
    ],'wrapper' => ['class' => 'form-group col-md-12']
])
			// ->add("idea_about" , "static", ["tag" => "div" , "label" => "Idea is about" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
            ->add("idea_about" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->idea_about, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Idea is about" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
            ->add("is_your_idea" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->is_your_idea, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Is Your Idea(Sector)" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
            //->add("is_your_idea" , "static", ["tag" => "div" , "label" => "Is Your Idea" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("about_startup" , "static", ["tag" => "div" , "label" => "One Line About Your Startup" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("problem_of_address" , "static", ["tag" => "div" , "label" => "What is the problem that you are going to address?" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("solution_of_problem" , "static", ["tag" => "div" , "label" => "What are the solutions for the problem addressed?" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("unique_selling_proposition" , "static", ["tag" => "div" , "label" => "What's your startup's Unique Selling Proposition (USP)?" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("revenue_stream" , "static", ["tag" => "div" , "label" => "How does your startup generate revenue (Write in your own words)?" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("duration" , "static", ["tag" => "div" , "label" => "How long have you been working on it?" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("is_won" , "static", ["tag" => "div" , "label" => "Won any seed grant/prize money with the same idea?","value" => CrudHelper::formatRows($this->model->is_won, 'radio', '1:Yes,0:No,:No', $this->model, '') , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("pitch_training" , "static", ["tag" => "div" , "label" => "Have you previously taken part in any pitch training?","value" => CrudHelper::formatRows($this->model->pitch_training, 'radio', '1:Yes,0:No,:No', $this->model, '') , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("is_incubated" , "static", ["tag" => "div" , "label" => "Whether your startup is incubated in any Technology Business Incubator?","value" => CrudHelper::formatRows($this->model->is_incubated, 'radio', '1:Yes,0:No,:No', $this->model, '') , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("demo_link" , "static", ["tag" => "div" , "label" => "Do you have any website / application link / demo that you would like to share?" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("about_tnsi" , "static", ["tag" => "div" , "label" => "Where did you hear about TNSI?" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("description" , "static", ["tag" => "div" , "label" => "Detailed description about your Idea" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-12'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
