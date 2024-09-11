<?php

namespace Impiger\SimpleSlider\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\SimpleSlider\Http\Requests\SimpleSliderItemRequest;
use Impiger\SimpleSlider\Models\SimpleSliderItem;

class SimpleSliderItemForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setupModel(new SimpleSliderItem)
            ->setValidatorClass(SimpleSliderItemRequest::class)
            ->withCustomFields()
            ->add('simple_slider_id', 'hidden', [
                'value' => request()->input('simple_slider_id'),
            ])
            ->add('title', 'text', [
                'label'      => trans('core/base::forms.title'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'data-counter' => 120,
                ],
            ])
            ->add('link', 'text', [
                'label'      => trans('core/base::forms.link'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'http://',
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'textarea', [
                'label'      => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows'         => 4,
                    'placeholder'  => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 2000,
                ],
            ])
            ->add('order', 'number', [
                'label'         => trans('core/base::forms.order'),
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'placeholder' => trans('core/base::forms.order_by_placeholder'),
                ],
                'default_value' => 0,
            ])
            ->add('image', 'mediaFile', [
                'label'      => 'Image/Video',
                'label_attr' => ['class' => 'control-label required'],
            ])
            ->add('close', 'button', [
                'label' => trans('core/base::forms.cancel'),
                'attr'  => [
                    'class'               => 'btn btn-warning',
                    'data-fancybox-close' => true,
                ],
            ])
            ->add('submit', 'submit', [
                'label' => trans('core/base::forms.save'),
                'attr'  => [
                    'class' => 'btn btn-info float-right',
                ],
            ]);
    }
}
