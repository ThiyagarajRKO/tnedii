<?php

namespace Impiger\InnovationVoucherProgram\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\InnovationVoucherProgram\Http\Requests\InnovationVoucherProgramRequest;
use Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram;
use DB;
use App\Utils\CrudHelper;
use Impiger\Workflows\Traits\WorkflowProperty;

class InnovationVoucherProgramForm extends FormAbstract {

    use WorkflowProperty;
    /**
     * {@inheritDoc}
     */
    public function buildForm() {
        $pathInfo = $this->request->getPathInfo();
        if ((isset($this->formOptions['isView']) && $this->formOptions['isView']) || str_contains($pathInfo, 'viewdetail')) {
            return $this->viewForm();
        }
        \Assets::addStylesDirectly(['vendor/core/plugins/gallery/css/admin-gallery.css'])
                ->addScriptsDirectly(['vendor/core/plugins/crud/js/multi-file-upload.js'])
                ->addScripts(['sortable']);

        $this
                ->setFormOption('template', 'module.form-template')
                ->setupModel(new InnovationVoucherProgram)
                ->setValidatorClass(InnovationVoucherProgramRequest::class)
                ->withCustomFields()
                ->add("custom_html_main_open", "html", ["html" => "<div class='row'>"])
                ->add("custom_html_open_0", "html", ["html" => "<div class='col-md-12 grouppedLayout innovation_voucher_program'>
                    <fieldset><div class='row'>"])
                ->add("id", "hidden", ["label" => "Id", "label_attr" => ["class" => "control-label"], "attr" => [""], "rules" => "sometimes|required", 'wrapper' => ['class' => 'form-group col-md-4']])
                ->add("created_by", "hidden", ["label" => "Created By", "label_attr" => ["class" => "control-label"], "attr" => [""], "rules" => "", 'wrapper' => ['class' => 'form-group col-md-4'],'value'=>\Auth::id()])
				->add("voucher_type", "customSelect", ["label" => "Voucher Type", "label_attr" => ["class" => "control-label required "], "attr" => ["class" => "select-full", 'data-field_index' => '2'], "choices" => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="voucher_type"', '', $this->model, '', $this->getName(), ''), 'wrapper' => ['class' => 'form-group col-md-4'], "empty_value" => "Select", "rules" => "required"])
                ->add("application_number", "text", ["label" => "Application Number", "label_attr" => ["class" => "control-label  "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '4', 'disabled' => CrudHelper::isFieldDisabled('edit'), 'readonly' => true], "value" => CrudHelper::generateCustomCode($this->model->application_number, "innovation_voucher_programs", "id", "VA-000", ""), "rules" => ""])
                ->add("project_title", "text", ["label" => "Project Title", "label_attr" => ["class" => "control-label required "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '3'], "rules" => "required"])
                ->add("mobile_number", "text", ["label" => "Mobile", "label_attr" => ["class" => "control-label required "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '5'], "rules" => "required"])
                ->add("email_id", "text", ["label" => "Email", "label_attr" => ["class" => "control-label required "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '6'], "rules" => "required"])
                ->add("custom_html_close_0", "html", ["html" => "</div></div>"])
                ->add("custom_html_open_1", "html", ["html" => "<div class='col-md-12 grouppedLayout innovation_voucher_program'>
                    <fieldset><legend class='grouppedLegend'> Detailed Project Description</legend><div class='row'>"])
                ->add("problem_of_sector", "textarea", ["label" => "The problem in the sector in which the innovation is proposed", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '1'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("scope_objective", "textarea", ["label" => "The scope & objectives of the project", "label_attr" => ["class" => "control-label required mb-25"], "attr" => ["rows" => 4, 'data-field_index' => '2'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("outcomes_deliverables", "textarea", ["label" => "The activities envisaged to attain the proposed outcome and deliverables", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '3'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("role_of_knowledge_partner", "textarea", ["label" => "The role of the Knowledge Partner and the applicant", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '4'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("budjet", "textarea", ["label" => "The Budjet", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '5'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("team_capability", "textarea", ["label" => "The capability of the applicant and it's team", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '6'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("nature_of_innovation", "textarea", ["label" => "The nature of innovation", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '7'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("impact", "textarea", ["label" => "Impact", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '8'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("custom_html_close_1", "html", ["html" => "</div></div>"])
                ->add("custom_html_open_2", "html", ["html" => "<div class='col-md-12 grouppedLayout innovation_voucher_program'>
                    <fieldset><legend class='grouppedLegend'> Project Summary (<a href='/storage/ivp-templates/IVP-EvaluationCriteria.pdf' target='_blank'>View</a>)</legend><div class='row'>"])
                ->add("project_need", "textarea", ["label" => "NEED of the project", "label_attr" => ["class" => "control-label required mb-25"], "attr" => ["rows" => 4, 'data-field_index' => '1'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("competetive", "textarea", ["label" => "COMPETITIVE ADVANTAGE", "label_attr" => ["class" => "control-label required mb-25"], "attr" => ["rows" => 4, 'data-field_index' => '2'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("level_of_impact", "textarea", ["label" => "LEVEL OF IMPACT/CHANGE the project could bring through the innovation", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '3'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("capability_capacity", "textarea", ["label" => "CAPABILITY AND CAPACITY of the applicant to achieve the innovation", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '4'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("collabaration_with_knowledge_partner", "textarea", ["label" => "COLLABORATION with Knowledge Partner would achieve advancement in innovation", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '5'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("custom_html_close_2", "html", ["html" => "</div></div>"])
                ->add("custom_html_open_3", "html", ["html" => "<div class='col-md-12 grouppedLayout innovation_voucher_program'>
                    <fieldset><div class='row'>"])
                ->add("pitch_for_your_project", "textarea", ["label" => "Give a One Sentence Pitch for your project", "label_attr" => ["class" => "control-label required "], "attr" => ["rows" => 4, 'data-field_index' => '1'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required"])
                ->add("project_based", "customRadio", ["label" => "Is your Project Manufacturing / Service based", "label_attr" => ["class" => "control-label required "], "choices" => CrudHelper::getRadioOptionValues('datalist', 'Manufacturing:Manufacturing|Service:Service'), 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '2'], "rules" => "required"])
                ->add("main_sector", "customSelect", ["label" => "Main Sector to which this project is related", "label_attr" => ["class" => "control-label required "], "attr" => ["class" => "select-full", 'data-field_index' => '3'], "choices" => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="sector"', '', $this->model, '', $this->getName(), ''), 'wrapper' => ['class' => 'form-group col-md-4'], "empty_value" => "Select", "rules" => "required"])
                ->add("additional_sector", "customSelect", ["label" => "Additional Sectors (if applicable) Multiple sectors may be selected", "label_attr" => ["class" => "control-label required mb-25"], "attr" => ["class" => "select-full", 'data-field_index' => '4', 'multiple' => true], "choices" => CrudHelper::getSelectOptionValues('external', '', 'attribute_options', 'id', 'name', 'attribute="sector"', '', $this->model, '', $this->getName(), ''), 'wrapper' => ['class' => 'form-group col-md-8'], "rules" => "required"])
                ->add("duration", "text", ["label" => "Duration for completion of the project ( in no. of months )", "label_attr" => ["class" => "control-label required "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '5'], "rules" => "required"])
                ->add("envisaged_timeline", "repeater", [
                    'label' => __('Envisaged timeline with project milestones (Maximum permitted timeline is one year)'),
                    'label_attr' => ['class' => 'control-label'],
                    'fields' => [
                        [
                            'type' => 'text',
                            'label' => __('Activities'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'email_id',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                        [
                            'type' => 'text',
                            'label' => __('Deliverables'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'mobile_number',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                        [
                            'type' => 'text',
                            'label' => __('Timeline (in months)'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'college_name',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                    ], 'wrapper' => ['class' => 'form-group col-md-12']
                ])
                ->add("project_cost", "text", ["label" => "Total cost of the project (Rs)", "label_attr" => ["class" => "control-label required "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '7'], "rules" => "required", 'help_block' => ['text' => "(Eligible Expenditures under IVP: <a href='/storage/ivp-templates/IVP-EligibleExpenditures.pdf' target='_blank'>View</a>)", 'tag' => 'p', 'attr' => ['class' => 'help-block']]])
                ->add("estimated_cost", CrudHelper::getFileType("mediaFile"), ["label" => "Estimated Cost of the Project ( use only prescribed format ) ", "label_attr" => ["class" => "control-label required "], rv_media_handle_upload(request()->file("file"), 0, ""), 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "required", 'help_block' => ['text' => "(Download, Fill and upload the prescribed Format: <a href='/storage/ivp-templates/FinanceFormatNew.docx' target='_blank'>View</a>)", 'tag' => 'p', 'attr' => ['class' => 'help-block']]])
                ->add("presentation", CrudHelper::getFileType("mediaFile"), ["label" => "Presentation", "label_attr" => ["class" => "control-label  "], rv_media_handle_upload(request()->file("file"), 0, ""), 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "", 'help_block' => ['text' => "(Download, Fill and upload the presentation: <a href='/storage/ivp-templates/IVPppttemplate.ppt' target='_blank'>View</a>)", 'tag' => 'p', 'attr' => ['class' => 'help-block']]])
                ->add('attachments', 'textarea', [
                    'template' => 'plugins/innovation-voucher-program::multifile-box',
                    'data' => $this->model
                ])
                ->add("reference_link", "text", ["label" => "Reference Link", "label_attr" => ["class" => "control-label  "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '7'], "rules" => ""])
                ->add('body-kp', 'textarea', ['template' => 'plugins/innovation-voucher-program::knowledge-partner'])
                // ->add("state", "text", ["label" => "State", "label_attr" => ["class" => "control-label required "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['data-field_index' => '12'], "rules" => "required"])
                ->add("district_id", "customSelect", ["label" => "District", "label_attr" => ["class" => "control-label required "], "attr" => ["class" => "select-full", 'data-field_index' => '13'], "choices" => CrudHelper::getSelectOptionValues('external', '', 'district', 'id', 'name', '', '', $this->model, '', $this->getName(), ''), 'wrapper' => ['class' => 'form-group col-md-4'], "empty_value" => "Select", "rules" => "required"])
                //->add("identified_knowledge_partner", "customRadio", ["label" => "Identified Knowledge Partner", "label_attr" => ["class" => "control-label required "], "choices" => CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No|2:Need assistance to identify a Knowledge Partner'), 'wrapper' => ['class' => 'form-group col-md-12'], "rules" => "required"])
               ->add('body', 'textarea', ['template' => 'plugins/innovation-voucher-program::declaration'])
                ->add("is_agree", "checkbox", ["label" => "I confirm all the declaration", "label_attr" => ["class" => "control-label  required"], "attr" => ["rows" => 4, 'data-field_index' => '15'], 'wrapper' => ['class' => 'form-group col-md-4'], "rules" => "", "value" => 1])
                ->add("display_layout_type", "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_3", "html", ["html" => "</div></div>"])
                ->addAfter('reference_link', 'ivp_company_details', 'form', [
                    'class' => 'Impiger\InnovationVoucherProgram\Forms\IvpCompanyDetailsForm',
                    'label' => false,
                    'wrapper' => [
                        'class' => 'form-group col-md-12'
                    ]
                ])
                ->addAfter('district_id', 'ivp_knowledge_partners', 'form', [
                    'class' => 'Impiger\InnovationVoucherProgram\Forms\IvpKnowledgePartnerForm',
                    'label' => false,
                    'wrapper' => [
                        'class' => 'form-group col-md-12'
                    ]
                ])
                ->add("custom_html_main_close", "html", ["html" => "</div>"])
                ->setActionButtons(view('module.form-actions')->render());
    }

    /**
     * {@inheritDoc}
     */
    public function viewForm() {

        $this->model = (\Arr::get($this->model, 'innovation_voucher_programs') && !\Arr::has($this->model, 'innovation_voucher_programs.0')) ? (object) $this->model['innovation_voucher_programs'] : $this->model;

        if (!isset($this->model->id)) {
            $this->model = InnovationVoucherProgram::getModel();
        }
          $isWorkflowSupport = $this->isWorkflowSupport($this->model->getTable());
          $this->setFormOption('isWorkflowSupport', $isWorkflowSupport);

        $this
                ->setupModel(new InnovationVoucherProgram)
                ->setTitle(page_title()->getTitle())
                ->setValidatorClass(InnovationVoucherProgramRequest::class)
                ->withCustomFields()
                ->setFormOption('class', 'viewForm')
                ->add("custom_html_main_open", "html", ["html" => "<div class='row previewForm' id='printPreviewForm'>"])
                ->add("custom_html_open_0", "html", ["html" => "<div class='col-md-12 grouppedLayout innovation_voucher_program'>
                    <fieldset><div class='row'>"])
                ->add("voucher_type", "static", ["tag" => "div", "value" => CrudHelper::formatRows($this->model->voucher_type, 'database', 'attribute_options|id|name', $this->model, ''), "label" => "Voucher Type", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("application_number", "static", ["tag" => "div", "label" => "Application Number", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("project_title", "static", ["tag" => "div", "label" => "Project Title", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("mobile_number", "static", ["tag" => "div", "label" => "Mobile", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("email_id", "static", ["tag" => "div", "label" => "Email", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("custom_html_close_0", "html", ["html" => "</div></div>"])
                ->add("custom_html_open_1", "html", ["html" => "<div class='col-md-12 grouppedLayout innovation_voucher_program'>
                    <fieldset><legend class='grouppedLegend'> Detailed Project Description</legend><div class='row'>"])
                ->add("problem_of_sector", "static", ["tag" => "div", "label" => "The problem in the sector in which the innovation is proposed", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("scope_objective", "static", ["tag" => "div", "label" => "The scope & objectives of the project", "label_attr" => ["class" => "control-label mb-25"], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("outcomes_deliverables", "static", ["tag" => "div", "label" => "The activities envisaged to attain the proposed outcome and deliverables", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("role_of_knowledge_partner", "static", ["tag" => "div", "label" => "The role of the Knowledge Partner and the applicant", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("budjet", "static", ["tag" => "div", "label" => "The Budjet", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("team_capability", "static", ["tag" => "div", "label" => "The capability of the applicant and it's team", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("nature_of_innovation", "static", ["tag" => "div", "label" => "The nature of innovation", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("impact", "static", ["tag" => "div", "label" => "Impact", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("custom_html_close_1", "html", ["html" => "</div></div>"])
                ->add("custom_html_open_2", "html", ["html" => "<div class='col-md-12 grouppedLayout innovation_voucher_program'>
                    <fieldset><legend class='grouppedLegend'> Project Summary</legend><div class='row'>"])
                ->add("project_need", "static", ["tag" => "div", "label" => "NEED of the project", "label_attr" => ["class" => "control-label mb-25"], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("competetive", "static", ["tag" => "div", "label" => "COMPETITIVE ADVANTAGE", "label_attr" => ["class" => "control-label mb-25"], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("level_of_impact", "static", ["tag" => "div", "label" => "LEVEL OF IMPACT/CHANGE the project could bring through the innovation", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("capability_capacity", "static", ["tag" => "div", "label" => "CAPABILITY AND CAPACITY of the applicant to achieve the innovation", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("collabaration_with_knowledge_partner", "static", ["tag" => "div", "label" => "COLLABORATION with Knowledge Partner would achieve advancement in innovation", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("custom_html_close_2", "html", ["html" => "</div></div>"])
                ->add("custom_html_open_3", "html", ["html" => "<div class='col-md-12 grouppedLayout innovation_voucher_program'>
                    <fieldset><div class='row'>"])
                ->add("pitch_for_your_project", "static", ["tag" => "div", "label" => "Give a One Sentence Pitch for your project", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group singleCol col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("project_based", "static", ["tag" => "div", "label" => "Is your Project Manufacturing / Service based", "label_attr" => ["class" => "control-label mb-25"], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("main_sector", "static", ["tag" => "div", "label" => "Main Sector to which this project is related","value" => CrudHelper::formatRows($this->model->main_sector, 'database', 'attribute_options|id|name', $this->model, ''), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("additional_sector", "static", ["tag" => "div", 'value' => CrudHelper::getMultiSelectText('attribute_options', 'id', 'additional_sector', 'name', 'attribute="sector"', $this->model), "label" => "Additional Sectors (if applicable) Multiple sectors may be selected", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-8'], 'attr' => ['class' => 'customStaticCls']])
                ->add("duration", "static", ["tag" => "div", "label" => "Duration for completion of the project ( in no. of months )", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("envisaged_timeline", "repeater", [
                    'label' => __('Envisaged timeline with project milestones (Maximum permitted timeline is one year)'),
                    'label_attr' => ['class' => 'control-label'],
                    'fields' => [
                        [
                            'type' => 'text',
                            'label' => __('Activities'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'email_id',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                        [
                            'type' => 'text',
                            'label' => __('Deliverables'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'mobile_number',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                        [
                            'type' => 'text',
                            'label' => __('Timeline (in months)'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'college_name',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                    ], 'wrapper' => ['class' => 'form-group col-md-12']
                ])
                ->add("project_cost", "static", ["tag" => "div", "label" => "Total cost of the project (Rs)", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
               ->add("estimated_cost" , "static", ["tag" => "a" , "label" => "Estimated Cost of the Project" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls' ,'href' =>'/storage/'.$this->model->estimated_cost ,'target'=>'_blank']])
               ->add("presentation" , "static", ["tag" => "a" , "label" => "Presentation" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls' ,'href' =>'/storage/'.$this->model->presentation ,'target'=>'_blank']])
                ->add('attachments', 'textarea', [
                    'template' => 'plugins/innovation-voucher-program::multifile-view'
                ])
                ->add("reference_link", "static", ["tag" => "div", "label" => "Reference Link", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                // ->add("state", "static", ["tag" => "div", "label" => "State", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("district_id", "static", ["tag" => "div","value" => CrudHelper::formatRows($this->model->district_id, 'database', 'district|id|name', $this->model, ''), "label" => "District", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                // ->add("identified_knowledge_partner", "static", ["tag" => "div","value" => CrudHelper::formatRows($this->model->identified_knowledge_partner, 'radio', '1:Yes,0:No,2:Need assistance to identify a Knowledge Partner', $this->model, ''), "label" => "Identified Knowledge Partner", "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("display_layout_type", "html", ["html" => "<span class='layoutDisplayType' data-display_type = 'vertical'></span> "])->add("custom_html_close_3", "html", ["html" => "</div></div>"])
                ->addAfter('reference_link', 'ivp_company_details', 'form', [
                    'class' => 'Impiger\InnovationVoucherProgram\Forms\IvpCompanyDetailsForm',
                    'label' => false,
                    'wrapper' => [
                        'class' => 'form-group col-md-12'
                    ]
                ])
                ->addAfter('district_id', 'ivp_knowledge_partners', 'form', [
                    'class' => 'Impiger\InnovationVoucherProgram\Forms\IvpKnowledgePartnerForm',
                    'label' => false,
                    'wrapper' => [
                        'class' => 'form-group col-md-12'
                    ]
                ])
                ->add("custom_html_main_close", "html", ["html" => "</div>"])
        ;
    }

}
