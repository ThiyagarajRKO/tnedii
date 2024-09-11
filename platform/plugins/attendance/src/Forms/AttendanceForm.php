<?php

namespace Impiger\Attendance\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Attendance\Http\Requests\AttendanceRequest;
use Impiger\Attendance\Models\Attendance;
use DB;
use App\Utils\CrudHelper;


class AttendanceForm extends FormAbstract
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
            
            ->setupModel(new Attendance)
            ->setValidatorClass(AttendanceRequest::class)
            ->withCustomFields()
            ->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required"])
			->add("financial_year_id" , "customSelect", ["label" => "Financial Year Id", "label_attr" => ["class" => "control-label  "],"attr" => ["class" => "select-full",'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'financial_year', 'id', 'session_year', '', '', $this->model, '', $this->getName(), ''),"empty_value" => "Select", "rules" => ""])
			->add("attendance_date" , "date", ["label" => "Attendance Date", "label_attr" => ["class" => "control-label"],'attr' => ['data-date-format' => 'yyyy-mm-dd' ],"default_value"=>"","rules" => ""])
			->add("present" , "radio", ["label" => "Present", "label_attr" => ["class" => "control-label"],"attr" => ["class" => "control-label",]])
			->add("absent" , "radio", ["label" => "Absent", "label_attr" => ["class" => "control-label"],"attr" => ["class" => "control-label",]])
			->add("annual_action_plan_id" , "customSelect", ["label" => "Annual Action Plan Id", "label_attr" => ["class" => "control-label"],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'financial_year_id','data-dd_parentkey' => 'financial_year_id','data-dd_table' => 'annual_action_plan','data-dd_key' => 'id','data-dd_lookup' => 'name' ,'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'annual_action_plan', 'id', 'name', '', 'financial_year_id', $this->model, 'financial_year_id', $this->getName(), ''),"empty_value" => "Select", "rules" => ""])
			->add("training_title_id" , "customSelect", ["label" => "Training Title Id", "label_attr" => ["class" => "control-label"],"attr" => ["class" => "select-full",'data-dd_qry_filterkey' => 'annual_action_plan_id','data-dd_parentkey' => 'annual_action_plan_id','data-dd_table' => 'training_title','data-dd_key' => 'id','data-dd_lookup' => 'code|venue' ,'data-field_index' => '0'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'training_title', 'id', 'code|venue', '', 'annual_action_plan_id', $this->model, 'annual_action_plan_id', $this->getName(), ''),"empty_value" => "Select", "rules" => ""])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'attendances')  && !\Arr::has($this->model, 'attendances.0')) ?(object) $this->model['attendances'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = Attendance::getModel();
        }
        
        
        $this
            
            ->setupModel(new Attendance)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(AttendanceRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("id" , "static", ["tag" => "div" , "label" => "Id" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("financial_year_id" , "static", ["tag" => "div" , "label" => "Financial Year Id" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("attendance_date" , "static", ["tag" => "div" ,'value' => CrudHelper::formatDate($this->model->attendance_date), "label" => "Attendance Date" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("present" , "static", ["tag" => "div" , "label" => "Present" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("absent" , "static", ["tag" => "div" , "label" => "Absent" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("annual_action_plan_id" , "static", ["tag" => "div" , "label" => "Annual Action Plan Id" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			->add("training_title_id" , "static", ["tag" => "div" , "label" => "Training Title Id" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			// ->addAfter("financial_year_id","annual_action_plan_id" , "static", ["tag" => "div" ,"value" => $this->model->join_fields()->annual_action_plan_id, "label" => "Annual Action Plan Id" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			// ->addAfter("financial_year_id","training_title_id" , "static", ["tag" => "div" ,"value" => $this->model->join_fields()->training_title_id, "label" => "Training Title Id" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
            // ->addAfter("id","entrepreneur_id" , "static", ["tag" => "div" ,"value" => CrudController::formatRows($this->model->entrepreneur_id, 'database', 'entrepreneurs|id|name', $this->model, ''), "label" => "Entrepreneur" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			// ->addAfter("absent","entrepreneur_id" , "static", ["tag" => "div" ,"value" => $this->model->join_fields()->entrepreneur_id, "label" => "Entrepreneur Id" , "label_attr" => ["class" => "control-label "],'attr' => ['class' => 'customStaticCls']])
			;
    }
    
}
