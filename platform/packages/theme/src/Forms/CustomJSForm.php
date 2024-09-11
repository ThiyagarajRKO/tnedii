<?php

namespace Impiger\Theme\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Models\BaseModel;
use Impiger\Theme\Http\Requests\CustomJsRequest;

class CustomJSForm extends FormAbstract
{
    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new BaseModel)
            ->setUrl(route('theme.custom-js.post'))
            ->setValidatorClass(CustomJsRequest::class)
            ->add('header_js', 'textarea', [
                'label'      => trans('packages/theme::theme.custom_header_js'),
                'label_attr' => ['class' => 'control-label'],
                'value'      => setting('custom_header_js'),
                'help_block' => [
                    'text' => trans('packages/theme::theme.custom_header_js_placeholder'),
                ],
            ])
            ->add('body_js', 'textarea', [
                'label'      => trans('packages/theme::theme.custom_body_js'),
                'label_attr' => ['class' => 'control-label'],
                'value'      => setting('custom_body_js'),
                'help_block' => [
                    'text' => trans('packages/theme::theme.custom_body_js_placeholder'),
                ],
            ])
            ->add('footer_js', 'textarea', [
                'label'      => trans('packages/theme::theme.custom_footer_js'),
                'label_attr' => ['class' => 'control-label'],
                'value'      => setting('custom_footer_js'),
                'help_block' => [
                    'text' => trans('packages/theme::theme.custom_footer_js_placeholder'),
                ],
            ]);
    }
}
