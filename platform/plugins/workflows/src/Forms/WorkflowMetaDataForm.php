<?php

namespace Impiger\Workflows\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Workflows\Http\Requests\WorkflowTransitionRequest;
use Impiger\Workflows\Models\WorkflowTransition;
use CustomWorkflow;

class WorkflowMetaDataForm extends FormAbstract
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
            <fieldset><legend class='grouppedLegend'> Workflow MetaData</legend><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],])
            ->add('state_group_name', 'text', [
                'label' => 'State group Name',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'State group Name'
                ],
                'wrapper' => ['class' => 'form-group col-md-4']
            ])          
            ->add('group_transition', 'customSelect', [
                'label' => 'Group Transition',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'select-full',
                    'multiple'=>true
                ]
                ,"choices"    => CustomWorkflow::getWorkflowAllStates('vendors'),
                'wrapper' => ['class' => 'form-group col-md-4']
            ])
            ->add("custom_html_close_0" , "html", ["html" => "</fieldset></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ;
    }
}
