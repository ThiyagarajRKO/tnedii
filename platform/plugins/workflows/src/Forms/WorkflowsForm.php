<?php

namespace Impiger\Workflows\Forms;

use App\Utils\CrudHelper;
use Impiger\Base\Forms\FormAbstract;
use Impiger\Workflows\Http\Requests\WorkflowsRequest;
use Impiger\Workflows\Models\Workflows;
use Impiger\Workflows\Support\WorkflowsSupport;
use Impiger\ACL\Models\Role;
use Assets;
use Arr;

class WorkflowsForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {

        $modulePropChoices = [];
        if(Arr::has($this->model, 'id')) {
            $modulePropChoices = \App\Utils\CrudHelper::getTableField(Arr::get($this->model, 'module_controller'));
        }

        $moduleChoices = \App\Models\Crud::where(['is_workflow_support' => 1])->get()->pluck('module_name', 'module_db')->toArray();
        $emptyArr = ["" => "Select"];
        $moduleChoices = array_merge($emptyArr, $moduleChoices);
        Assets::addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
        ])
        ->addScriptsDirectly([
            'vendor/core/plugins/crud/js/crud_utils.js',
        'vendor/core/core/base/js/common_utils.js',
            'vendor/core/plugins/workflows/js/workflow_config.js'
        ]);

		$this   
		->setFormOption('template', 'module.form-template')
		->setupModel(new Workflows)
		->setTitle(page_title()->getTitle())
		->setValidatorClass(WorkflowsRequest::class)
		->withCustomFields()
		->setFormOption('class','workflow-config-form')
		->setFormOption('id','workflow-config-form')
        ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
        ->add('name', 'text', [
            'label' => trans('core/base::forms.title'),
            'label_attr' => ['class' => 'control-label required'],
            'attr' => [
                'placeholder' => trans('core/base::forms.title'),
                'data-counter' => 120,
            ],
            'wrapper' => ['class' => 'form-group col-md-4']
        ])
        ->add('module_controller', 'customSelect', [
            'label' => 'Module',
            'label_attr' => ['class' => 'control-label required'],
            'attr' => [
                'class' => 'select-full'
            ],
            'wrapper' => ['class' => 'form-group col-md-4'],
            'choices' =>  $moduleChoices
        ])
        ->add('module_property', 'customSelect', [
            'label' => 'Module Property',
            'label_attr' => ['class' => 'control-label required'],
            'attr' => [
                'class' => 'select-full'
            ],
            'wrapper' => ['class' => 'form-group col-md-4'],
            'choices' =>  $modulePropChoices
        ])
        ->add('initial_state', 'text', [
            'label' => 'Initial State',
            'label_attr' => ['class' => 'control-label required'],
            'attr' => [
                'placeholder' => 'Initial State',
            ],
            'wrapper' => ['class' => 'form-group col-md-4']
        ])->add("custom_html_main_close" , "html", ["html" => "</div>"])
		->add('transitions', 'collection', [
            'type' => 'form',
            'label' => false,
            'options' => [
                'class' => 'Impiger\Workflows\Forms\WorkflowTransitionForm',
                'label' => false,
                ],
                'wrapper' => [
                    'class' => 'subFormRepeater'
                ]
        ])
		->setActionButtons(view('module.form-actions')->render());
    }
}
