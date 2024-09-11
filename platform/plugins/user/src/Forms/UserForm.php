<?php

namespace Impiger\User\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\User\Http\Requests\UserRequest;
use Impiger\User\Models\User;
use DB;
use App\Utils\CrudHelper;

class UserForm extends FormAbstract
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
            ->setupModel(new User)
            ->setValidatorClass(UserRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout user'>
                    <fieldset><legend class='grouppedLegend'> Personal</legend><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("user_id" , "hidden", ["label" => "User Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("email" , "text", ["label" => "Email ID", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '3','disabled' => CrudHelper::isFieldDisabled('edit-profile')], "rules" => "required"])
			->add("first_name" , "text", ["label" => "First Name", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '4'], "rules" => "required"])
			->add("last_name" , "text", ["label" => "Last Name", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '5'], "rules" => ""])
			->add("phone_number" , "text", ["label" => "Phone Number", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '6'], "rules" => ""])
			->add("designation" , "text", ["label" => "Designation", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '7'], "rules" => ""])
			->add("photo" , CrudHelper::getFileType("mediaImage"), ["label" => "Photo", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'], "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
//			->add("custom_html_open_1" , "html", ["html" => "<div class='col-md-12 grouppedLayout user'>
//                    <fieldset><legend class='grouppedLegend'> Address</legend>"])
//			->add('user_addresses', 'form', [
//                        'class' => 'Impiger\User\Forms\UserAddressForm',
//                        'label' => false,
//                        'wrapper' => [
//                            'class' => 'form-group '
//                        ]
//                    ])
//			->add("custom_html_close_1" , "html", ["html" => "</div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])

            ->setActionButtons(view('module.form-actions')->render());

    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'users')  && !\Arr::has($this->model, 'users.0')) ?(object) $this->model['users'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = User::getModel();
        }

        $this

            ->setupModel(new User)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(UserRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout user'>
                    <fieldset><legend class='grouppedLegend'> Personal</legend><div class='row'>"])


			->add("email" , "static", ["tag" => "div" , "label" => "Email ID" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("first_name" , "static", ["tag" => "div" , "label" => "First Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("last_name" , "static", ["tag" => "div" , "label" => "Last Name" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("photo" , "mediaImage", ["label" => "Photo", "label_attr" => ["class" => "control-label  "],rv_media_handle_upload(request()->file("file"), 0, ""),'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
//			->add("custom_html_open_1" , "html", ["html" => "<div class='col-md-12 grouppedLayout user'>
//                    <fieldset><legend class='grouppedLegend'> Address</legend>"])
//			->add('user_addresses', 'form', [
//                        'class' => 'Impiger\User\Forms\UserAddressForm',
//                        'label' => false,
//                        'wrapper' => [
//                            'class' => 'form-group '
//                        ]
//                    ])
//			->add("custom_html_close_1" , "html", ["html" => "</div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }

}
