<?php

namespace Impiger\PasswordCriteria\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\PasswordCriteria\Http\Requests\PasswordCriteriaRequest;
use Impiger\PasswordCriteria\Models\PasswordCriteria;

class PasswordCriteriaForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new PasswordCriteria)
            ->setValidatorClass(PasswordCriteriaRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ]);
    }
}
