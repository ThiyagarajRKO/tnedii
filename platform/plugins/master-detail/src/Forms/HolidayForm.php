<?php

namespace Impiger\MasterDetail\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Http\Requests\HolidayRequest;
use Impiger\MasterDetail\Models\Holiday;
use DB;
use App\Utils\CrudHelper;


class HolidayForm extends FormAbstract
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
            
            ->setupModel(new Holiday)
            ->setValidatorClass(HolidayRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout holiday'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])
			->add("date" , "date", ["label" => "Date", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => ""])
			->add("title" , "text", ["label" => "Title", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '3'], "rules" => ""])
			->add("financial_year_id" , "customSelect", ["label" => "Financial Year", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-field_index' => '4'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'financial_year', 'id', 'session_year', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => ""])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'holidays')  && !\Arr::has($this->model, 'holidays.0')) ?(object) $this->model['holidays'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = Holiday::getModel();
        }
        
        
        $this
            
            ->setupModel(new Holiday)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(HolidayRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout holiday'>
                    <fieldset><div class='row'>"])
			
			->add("date" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->date), "label" => "Date" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("title" , "static", ["tag" => "div" , "label" => "Title" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("financial_year_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->financial_year_id, 'database', 'financial_year|id|session_year', $this->model, ''), "label" => "Financial Year" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
