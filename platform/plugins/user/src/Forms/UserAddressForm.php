<?php

namespace Impiger\User\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\User\Http\Requests\UserAddressRequest;
use Impiger\User\Models\UserAddress;
use DB;
use App\Utils\CrudHelper;

class UserAddressForm extends FormAbstract
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
            ->setupModel(new UserAddress)
            ->setValidatorClass(UserAddressRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout present_address_block'>
                    <fieldset><div class='row'>"])
			->add("present_add_1" , "text", ["label" => "Address Line 1", "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '1'], "rules" => ""])
			->add("present_add_2" , "text", ["label" => "Address Line 2", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['data-field_index' => '2'], "rules" => ""])
			->add("present_country" , "customSelect", ["label" => "Country", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-field_index' => '3'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'countries', 'id', 'country_name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => ""])
			->add("present_district" , "customSelect", ["label" => "Emirates", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'country_id','data-dd_parentkey' => 'present_country','data-dd_table' => 'district','data-dd_key' => 'id','data-dd_lookup' => 'name' ,'data-field_index' => '4'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'district', 'id', 'name', '', 'country_id', $this->model, 'present_country', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => ""])
			->add("present_county" , "customSelect", ["label" => "City", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'district_id','data-dd_parentkey' => 'present_district','data-dd_table' => 'county','data-dd_key' => 'id','data-dd_lookup' => 'name' ,'data-field_index' => '5'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'county', 'id', 'name', '', 'district_id', $this->model, 'present_district', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-4'],"empty_value" => "Select", "rules" => ""])
			->add("sm_openpresent_phonecode" , "html", ["html" => "<div class='form-group col-md-4'> <div class='form-group row'>"])->add("present_phonecode" , "customSelect", ["label" => "Ph. Code", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'id','data-dd_parentkey' => 'present_country','data-dd_table' => 'countries','data-dd_key' => 'id','data-dd_lookup' => 'phone_code' ,'data-field_index' => '6'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'countries', 'id', 'phone_code', 'phone_code IS NOT NULL', 'id', $this->model, 'present_country', $this->getName(), ''),'wrapper' => ['class' => 'form-group drop-down-sm col-md-4'],"empty_value" => "Select", "rules" => ""])
			->add("present_phone" , "text", ["label" => "Contact Number", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group drop-down-xm col-md-8'],'attr' => ['data-field_index' => '7'], "rules" => ""])->add("custom_html_dd_sm_closepresent_phone" , "html", ["html" => "</div></div>"])
			->add("present_zipcode" , "text", ["label" => "Zipcode", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-4 mt-1rem'],'attr' => ['data-field_index' => '8'], "rules" => ""])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-4']])
			->add("custom_html_close_0" , "html", ["html" => "</div></div>"])

			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_1" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])

            ->setActionButtons(view('module.form-actions')->render());

    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'user_addresses')  && !\Arr::has($this->model, 'user_addresses.0')) ?(object) $this->model['user_addresses'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = UserAddress::getModel();
        }

        $this

            ->setupModel(new UserAddress)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(UserAddressRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout present_address_block'>
                    <fieldset><div class='row'>"])
			->add("present_add_1" , "static", ["tag" => "div" , "label" => "Address Line 1" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("present_add_2" , "static", ["tag" => "div" , "label" => "Address Line 2" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("present_country" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->present_country, 'database', 'countries|id|country_name', $this->model, ''), "label" => "Country" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("present_district" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->present_district, 'database', 'district|id|name', $this->model, ''), "label" => "Emirates" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("present_county" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->present_county, 'database', 'county|id|name', $this->model, ''), "label" => "City" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("present_phonecode" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->present_phonecode, 'database', 'countries|id|phone_code', $this->model, ''), "label" => "Ph. Code" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("present_phone" , "static", ["tag" => "div" , "label" => "Contact Number" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
			->add("present_zipcode" , "static", ["tag" => "div" , "label" => "Zipcode" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])

			->add("custom_html_close_0" , "html", ["html" => "</div></div>"])

			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_1" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }

}
