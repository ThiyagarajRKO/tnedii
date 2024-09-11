<?php

namespace Impiger\KnowledgePartner\Forms;

use Assets;
use Impiger\Base\Forms\FormAbstract;
use Impiger\KnowledgePartner\Enums\KnowledgePartnerStatusEnum;
use Impiger\KnowledgePartner\Http\Requests\EditKnowledgePartnerRequest;
use Impiger\KnowledgePartner\Http\Requests\KnowledgePartnerRequest;
use Impiger\KnowledgePartner\Models\KnowledgePartner;

use App\Utils\CrudHelper;

class KnowledgePartnerForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $pathInfo = $this->request->getPathInfo();
        if ((isset($this->formOptions['isView']) && $this->formOptions['isView']) || str_contains($pathInfo, 'viewdetail')) {
            return $this->viewForm();
        }

        Assets::addScriptsDirectly('vendor/core/plugins/knowledge-partner/js/knowledge-partner.js')
            ->addStylesDirectly('vendor/core/plugins/knowledge-partner/css/knowledge-partner.css');

        $this
            ->setupModel(new KnowledgePartner)
            //->setValidatorClass(EditKnowledgePartnerRequest::class)
            //->withCustomFields()
            /*->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices'    => KnowledgePartnerStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status')*/
            ;
    }
    
    /**
     * {@inheritDoc}
     */
    public function viewForm()
    {

        $this
                ->setupModel(new KnowledgePartner)
                ->setTitle(page_title()->getTitle())
                ->setValidatorClass(KnowledgePartnerRequest::class)
                ->withCustomFields()
                ->setFormOption('class', 'viewForm')
                ->add("custom_html_main_open", "html", ["html" => "<div class='row previewForm' id='printPreviewForm'>"])
                ->add("custom_html_open_0", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>Basic Information of the Institution/Incubation Centre</legend><div class='row'>"])
                ->add("name_of_the_institution", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.name_of_the_institution'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("office_address", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.office_address'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("state_id" , "static", ["tag" => "div" ,"value" => CrudHelper::formatRows($this->model->state_id, 'database', '', $this->model, '1:states:state_id:state_name'), "label" => "State" , "label_attr" => ["class" => "control-label "],'wrapper' => ['class' => 'form-group col-md-4'],'attr' => ['class' => 'customStaticCls']])
                ->add("district", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.district'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("pin_code", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.pin_code'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("institution_registered_under_or_accredited_by", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.institution_registered_under_or_accredited_by'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("year_of_establishment_or_inception", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.year_of_establishment_or_inception'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("upload_proof_of_registration", 'textarea', [
                    'template' => 'plugins/knowledge-partner::multifile-view',
                    "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.upload_proof_of_registration')
                ])
                ->add("gst_no", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.gst_no'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("pan", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.pan'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("tin", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.tin'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("board_founders_director", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.board_founders_director'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("years_of_experience_in_incubation", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_incubation'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("ceo", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.ceo'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("lead_scientist_or_incubation_manager", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.lead_scientist_or_incubation_manager'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("years_of_experience_in_r_and_d", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_r_and_d'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("sources_of_financial_support", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.sources_of_financial_support'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("key_recognition_award_received_by_institute", 'textarea', [
                    'template' => 'plugins/knowledge-partner::multifile-view',
                    "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.key_recognition_award_received_by_institute')
                ])
                ->add("certifications", 'textarea', [
                    'template' => 'plugins/knowledge-partner::multifile-view',
                    'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.certifications'),
                ])
                ->add("contact_person", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.contact_person'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("mobile_number", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.mobile_number'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("email_address", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.email_address'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("website", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.website'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("custom_html_close_0", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_open_1", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>".trans('plugins/knowledge-partner::knowledge-partner.tables.sectors_core_competencies')."</legend><div class='row'>"])
                ->add("sectors_core_competencies", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.sectors_core_competencies'), 'label_show' => false, 'value' => ($this->model->sectors_core_competencies != "" ? implode(", ", json_decode($this->model->sectors_core_competencies, true)) : ""), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("custom_html_close_1", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_open_2", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>".trans('plugins/knowledge-partner::knowledge-partner.tables.research_development')."</legend><div class='row'>"])
                ->add("have_testing_lab_facilities", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.have_testing_lab_facilities'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("type_of_lab", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.type_of_lab'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4' . ($this->model->have_testing_lab_facilities == "Yes" ? "" : " d-none")], 'attr' => ['class' => 'customStaticCls']])
                ->add("area_in_sqft", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.area_in_sqft'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4' . ($this->model->have_testing_lab_facilities == "Yes" ? "" : " d-none")], 'attr' => ['class' => 'customStaticCls']])
                ->add("equipments", 'textarea', [
                    'template' => 'plugins/knowledge-partner::multifile-view',
                    'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.equipments'),
                    'wrapper' => ['class' => '' . ($this->model->have_testing_lab_facilities == "Yes" ? "" : " d-none")],
                ])
                ->add("lab_facility", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.lab_facility'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("service_charge", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.service_charge'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("concessions", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.concessions'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("custom_html_close_2", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_open_3", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>".trans('plugins/knowledge-partner::knowledge-partner.tables.facilities_available')."</legend><div class='row'>"])
                ->add("number_of_awareness_training_programs", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_awareness_training_programs'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("number_of_boot_camp_ideation_etc", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("number_of_boot_camp_ideation_etc_files", 'textarea', [
                    'template' => 'plugins/knowledge-partner::multifile-view',
                    'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc_files'),
                    'wrapper' => ['class' => '' . ($this->model->number_of_boot_camp_ideation_etc > 0 ? "" : " d-none")],
                ])
                ->add("have_you_accelerated_startups", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.have_you_accelerated_startups'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("total_number_of_startups_supported", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.total_number_of_startups_supported'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4' . ($this->model->have_you_accelerated_startups == "Yes" ? "" : " d-none")], 'attr' => ['class' => 'customStaticCls']])
                ->add("ivp_applications_and_sanctions", 'textarea', [
                    'template' => 'plugins/knowledge-partner::multifile-view',
                    'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.ivp_applications_and_sanctions'),
                ])
                ->add("custom_html_close_3", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_open_4", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>".trans('plugins/knowledge-partner::knowledge-partner.tables.technical_support_and_mentorship')."</legend><div class='row'>"])
                ->add("total_no_of_mentors_available", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.total_no_of_mentors_available'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("mentor_details", "repeater", [
                    'fields' => [
                        [
                            'type' => 'text',
                            'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_name'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'mentor_name',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                        [
                            'type' => 'text',
                            'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_qualification'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'mentor_qualification',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                        [
                            'type' => 'text',
                            'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_designation'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'mentor_designation',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                        [
                            'type' => 'text',
                            'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_date_of_joining_your_organization'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'mentor_date_of_joining_your_organization',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                        [
                            'type' => 'text',
                            'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_number_of_years_experience'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'mentor_number_of_years_experience',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                        [
                            'type' => 'text',
                            'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_how_many_innovators_has_guided_so_far'),
                            'label_attr' => ['class' => 'control-label required'],
                            'attributes' => [
                                'name' => 'mentor_how_many_innovators_has_guided_so_far',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapper' => ['class' => 'form-group col-md-4']
                        ],
                    ], 'wrapper' => ['class' => 'form-group col-md-12']
                ])
                ->add("custom_html_close_4", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_open_5", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>".trans('plugins/knowledge-partner::knowledge-partner.tables.ipr_related_registrations')."</legend><div class='row'>"])
                ->add("number_of_technologies_commercialized", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_technologies_commercialized'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("number_of_indian_or_wipo_compliant_patents_received", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_indian_or_wipo_compliant_patents_received'), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("custom_html_close_5", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_open_6", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>".trans('plugins/knowledge-partner::knowledge-partner.tables.financial_support_received_for_innovators')."</legend><div class='row'>"])
                ->add("financial_support_received_for_innovators", 'textarea', [
                    'template' => 'plugins/knowledge-partner::multifile-view',
                    'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.financial_support_received_for_innovators'),
                    'label_show' => false,
                ])
                ->add("custom_html_close_6", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_open_7", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>".trans('plugins/knowledge-partner::knowledge-partner.tables.your_financial_status')."</legend><div class='row'>"])
                ->add("your_financial_status", 'textarea', [
                    'template' => 'plugins/knowledge-partner::multifile-view',
                    'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.your_financial_status'),
                    'label_show' => false,
                ])
                ->add("custom_html_close_7", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_open_8", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>".trans('plugins/knowledge-partner::knowledge-partner.tables.land_and_buildings_on_date'). ' ' . $this->model->land_and_buildings_on_date ."</legend><div class='row'>"])
                ->add("land_and_buildings_as_on_date", 'textarea', [
                    'template' => 'plugins/knowledge-partner::multifile-view',
                    'label' => trans('plugins/knowledge-partner::knowledge-partner.tables.land_and_buildings_as_on_date'),
                    'label_show' => false,
                ])
                ->add("custom_html_close_8", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_open_9", "html", ["html" => "<div class='col-md-12 grouppedLayout knowledge_partner'>
                    <fieldset><legend class='grouppedLegend'>".trans('plugins/knowledge-partner::knowledge-partner.tables.knowledge_partner_and_innovatorrelationship')."</legend><div class='row'>"])
                ->add("knowledge_partner_and_innovatorrelationship", "static", ["tag" => "div", "label" => trans('plugins/knowledge-partner::knowledge-partner.tables.knowledge_partner_and_innovatorrelationship'), 'label_show' => false, 'value' => ($this->model->knowledge_partner_and_innovatorrelationship != "" ? implode(", ", json_decode($this->model->knowledge_partner_and_innovatorrelationship, true)) : ""), "label_attr" => ["class" => "control-label "], 'wrapper' => ['class' => 'form-group col-md-4'], 'attr' => ['class' => 'customStaticCls']])
                ->add("custom_html_close_9", "html", ["html" => "</div></div>"])
                
                ->add("custom_html_main_close", "html", ["html" => "</div>"])
        ;
    }
}
