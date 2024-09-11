<?php

namespace Impiger\Attendance\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Attendance\Http\Requests\AttendanceRemarkRequest;
use Impiger\Attendance\Models\AttendanceRemark;
use DB;
use App\Utils\CrudHelper;


class AttendanceRemarkForm extends FormAbstract
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
            
            ->setupModel(new AttendanceRemark)
            ->setValidatorClass(AttendanceRemarkRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout attendance_remark'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])
			->add("training_title_id" , "customSelect", ["label" => "Training Name & Code", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'training_title', 'id', 'name|code', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("entrepreneur_id" , "customSelect", ["label" => "Entrepreneur ", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '3'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'entrepreneurs', 'id', 'name', '', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("remark" , "textarea", ["label" => "Remark", "label_attr" => ["class" => "control-label required "], "attr"=>["rows" => 4,'data-field_index' => '4'],'wrapper' => ['class' => 'form-group col-md-6'], "rules" => "required"])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'attendance_remarks')  && !\Arr::has($this->model, 'attendance_remarks.0')) ?(object) $this->model['attendance_remarks'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = AttendanceRemark::getModel();
        }
        
        
        $this
            
            ->setupModel(new AttendanceRemark)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(AttendanceRemarkRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout attendance_remark'>
                    <fieldset><div class='row'>"])
			
			->add("training_title_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->training_title_id, 'database', 'training_title|id|name', $this->model, ''), "label" => "Training Name & Code" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("entrepreneur_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->entrepreneur_id, 'database', 'entrepreneurs|id|name', $this->model, ''), "label" => "Entrepreneur " , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("remark" , "static", ["tag" => "div" , "label" => "Remark" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
