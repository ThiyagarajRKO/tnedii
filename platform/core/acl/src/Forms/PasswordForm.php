<?php

namespace Impiger\ACL\Forms;

use Impiger\ACL\Http\Requests\UpdatePasswordRequest;
use Impiger\ACL\Models\User;
use Impiger\Base\Forms\FormAbstract;
use Html;

class PasswordForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     * @customized Sabari Shankar.Parthiban
     */
    public function buildForm()
    {
        $this
            ->setupModel(new User)
            ->setValidatorClass(UpdatePasswordRequest::class)
            ->setFormOption('template', 'core/base::forms.form-no-wrap')
            ->setFormOption('id', 'password-form')
            ->add('old_password', 'password', [
                'label'      => trans('core/acl::users.current_password'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'data-counter' => 60,
                ],
            ])
            ->add('rowOpen1', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('password', 'password', [
                'label'      => trans('core/acl::users.new_password'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
//                    'data-counter' => 60, /* Customized by Sabari Shankar.Parthiban*/
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
                'help_block' => [
                    'text' => Html::tag('span', 'Password Strength', ['class' => 'hidden'])->toHtml(),
                    'tag'  => 'div',
                    'attr' => [
                        'class' => 'pwstrength_viewport_progress',
                    ],
                ],
            ])
            ->add('password_confirmation', 'password', [
                'label'      => trans('core/acl::users.confirm_new_password'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
//                    'data-counter' => 60, /* Customized by Sabari Shankar.Parthiban*/
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
            ])
            ->add('rowClose', 'html', [
                'html' => '</div>',
            ])
            ->setActionButtons(view('core/acl::users.profile.actions')->render());
    }
}
