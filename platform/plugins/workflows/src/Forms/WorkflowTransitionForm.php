<?php

namespace Impiger\Workflows\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Workflows\Http\Requests\WorkflowTransitionRequest;
use Impiger\Workflows\Models\WorkflowTransition;
use CustomWorkflow;
use App\Utils\CrudHelper;
class WorkflowTransitionForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new WorkflowTransition)
            ->setValidatorClass(WorkflowTransitionRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout workflow-transition'>
            <fieldset><legend class='grouppedLegend'> Workflow Transition</legend><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],])
            ->add('name', 'text', [
                'label' => 'Transition Name',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Transition Name'
                ],
                'wrapper' => ['class' => 'form-group col-md-4']
            ])			
            ->add('from_state', 'text', [
                'label' => 'From State',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'From State'
                ],
                'wrapper' => ['class' => 'form-group col-md-4']
            ])
            ->add('to_state', 'text', [
                'label' => 'To State',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'To State'
                ],
                'wrapper' => ['class' => 'form-group col-md-4']
            ])
            ->add('action', 'customSelect', [
                'label' => 'Action',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'select-full'
                ]
                ,"choices"    => CustomWorkflow::supportedModuleActions(),
                'wrapper' => ['class' => 'form-group col-md-4']
            ])
            ->add('custom_input', 'text', [
                'label' => 'Custom Input',
                'label_attr' => ['class' => 'control-label'],                            
                'wrapper' => ['class' => 'form-group col-md-4'], 
            ])
            ->add('is_notification_enabled', 'customRadio', [
                'label' => 'Do you want to allow mail notification?',
                'label_attr' => ['class' => 'control-label'],  
                "choices"    => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No'),                         
                'wrapper' => ['class' => 'form-group col-md-4'], 
            ])
                
            ->add("custom_html_close_0" , "html", ["html" => "</fieldset></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('plugins/crud::module.form-actions')->render());
    }
}
