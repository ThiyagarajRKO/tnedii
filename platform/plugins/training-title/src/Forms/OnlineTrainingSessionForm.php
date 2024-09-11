<?php

namespace Impiger\TrainingTitle\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\TrainingTitle\Http\Requests\OnlineTrainingSessionRequest;
use Impiger\TrainingTitle\Models\OnlineTrainingSession;
use DB;
use App\Utils\CrudHelper;


class OnlineTrainingSessionForm extends FormAbstract
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
            
            ->setupModel(new OnlineTrainingSession)
            ->setValidatorClass(OnlineTrainingSessionRequest::class)
            ->withCustomFields()
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout online_training_session'>
                    <fieldset><div class='row'>"])
			->add("id" , "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"],"attr" => [""], "rules" => "sometimes|required",'wrapper' => ['class' => 'form-group col-md-6']])
			->add("header" , "customSelect", ["label" => "Header", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '2'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="online_sessions"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("title" , "text", ["label" => "Title", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '3'], "rules" => "required"])
			->add("sub_title" , "text", ["label" => "Sub Title", "label_attr" => ["class" => "control-label  "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '4'], "rules" => ""])
			->add("url" , "text", ["label" => "Url", "label_attr" => ["class" => "control-label required "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['data-field_index' => '5'], "rules" => "required"])
			->add("type" , "customSelect", ["label" => "Type", "label_attr" => ["class" => "control-label required "],"attr" => ["class" => "select-full",'data-field_index' => '6'],"choices"    => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="msme_scheme"', '', $this->model, '', $this->getName(), ''),'wrapper' => ['class' => 'form-group col-md-6'],"empty_value" => "Select", "rules" => "required"])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			
            ->setActionButtons(view('module.form-actions')->render());
             
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

		$this->model = (\Arr::get($this->model, 'online_training_sessions')  && !\Arr::has($this->model, 'online_training_sessions.0')) ?(object) $this->model['online_training_sessions'] : $this->model;

        if(!isset($this->model->id)) {
            $this->model = OnlineTrainingSession::getModel();
        }
        
        
        $this
            
            ->setupModel(new OnlineTrainingSession)
            ->setTitle(page_title()->getTitle())
            ->setValidatorClass(OnlineTrainingSessionRequest::class)
            ->withCustomFields()
			->setFormOption('class','viewForm')
            ->add("custom_html_main_open" , "html", ["html" => "<div class='row'>"])
			->add("custom_html_open_0" , "html", ["html" => "<div class='col-md-12 grouppedLayout online_training_session'>
                    <fieldset><div class='row'>"])
			
			->add("header" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->header, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Header" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("title" , "static", ["tag" => "div" , "label" => "Title" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("sub_title" , "static", ["tag" => "div" , "label" => "Sub Title" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("url" , "static", ["tag" => "div" , "label" => "Url" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("type" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->type, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Type" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-6'],'attr' => ['class' => 'customStaticCls']])
			->add("display_layout_type" , "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_0" , "html", ["html" => "</div></div>"])
			->add("custom_html_main_close" , "html", ["html" => "</div>"])
			;
    }
    
}
