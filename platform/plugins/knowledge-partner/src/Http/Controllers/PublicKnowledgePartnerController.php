<?php

namespace Impiger\KnowledgePartner\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
//use Impiger\KnowledgePartner\Http\Requests\KnowledgePartnerRequest;
use Illuminate\Http\Request;
use Impiger\KnowledgePartner\Repositories\Interfaces\KnowledgePartnerInterface;
use EmailHandler;
use Exception;
use Illuminate\Routing\Controller;

class PublicKnowledgePartnerController extends Controller
{
    /**
     * @var KnowledgePartnerInterface
     */
    protected $knowledgePartnerRepository;
    
    protected $step1_validation_arr = [
        'name_of_the_institution' => 'required',
        'office_address' => 'required',
        'state_id' => 'required',
        'district' => 'required',
        'pin_code' => 'required|integer',
        'institution_registered_under_or_accredited_by' => 'required',
        'year_of_establishment_or_inception' => 'required|integer',
        'upload_proof_of_registration_upload' => 'required|array',
        'upload_proof_of_registration_upload.*' => 'file|mimes:jpg,jpeg,pdf',
        'gst_no' => 'required',
        'pan' => 'required',
        'tin' => 'required',
        'board_founders_director' => 'required',
        'years_of_experience_in_incubation' => 'required|integer',
        'ceo' => 'required',
        'lead_scientist_or_incubation_manager' => 'required',
        'years_of_experience_in_r_and_d' => 'required|integer',
        'sources_of_financial_support' => 'required',
        //'key_recognition_award_received_by_institute' => 'required',
        //'certifications' => 'required',
        'contact_person' => 'required',
        'mobile_number' => 'required',
        'email_address' => 'required|email',
        'website' => 'required',
    ];
    protected $step2_validation_arr = [
        //'sectors_core_competencies' => 'required|min:1',
        //'sectors_core_competencies.*'  => 'required|string',
    ];
    protected $step3_validation_arr = [
        'have_testing_lab_facilities' => 'required',
        'type_of_lab'  => 'required_if:have_testing_lab_facilities,Yes',
        'area_in_sqft' => 'required_if:have_testing_lab_facilities,Yes|nullable|numeric',
        'equipments_upload' => 'required_if:have_testing_lab_facilities,Yes',
        'equipments_upload.*' => 'file|mimes:jpg,jpeg,pdf',
        'lab_facility' => 'required',
        'service_charge' => 'required|numeric',
        'concessions' => 'required',
    ];
    protected $step4_validation_arr = [
        'number_of_awareness_training_programs' => 'required|integer',
        'number_of_boot_camp_ideation_etc' => 'required|integer',
        'number_of_boot_camp_ideation_etc_files_upload' => 'required',
        'number_of_boot_camp_ideation_etc_files_upload.*' => 'file|mimes:jpg,jpeg,pdf',
        'have_you_accelerated_startups' => 'required',
        'total_number_of_startups_supported'  => 'required_if:have_you_accelerated_startups,Yes|nullable|integer',
        'ivp_applications_and_sanctions_upload' => 'required',
        'ivp_applications_and_sanctions_upload.*' => 'file|mimes:jpg,jpeg,pdf',
    ];
    protected $step5_validation_arr = [
        'total_no_of_mentors_available' => 'required|integer',
        'mentor_details' => 'required_unless:total_no_of_mentors_available,0|array',
        'mentor_details.*' => 'required_unless:total_no_of_mentors_available,0|array',
        'mentor_details.*.*' => 'required_unless:total_no_of_mentors_available,0',
    ];
    protected $step6_validation_arr = [
        'number_of_technologies_commercialized' => 'required',
        'number_of_indian_or_wipo_compliant_patents_received' => 'required',
    ];
    protected $step7_validation_arr = [
        'financial_support_received_for_innovators_upload' => 'required',
        'financial_support_received_for_innovators_upload.*' => 'file|mimes:jpg,jpeg,pdf',
        'your_financial_status_upload' => 'required',
        'your_financial_status_upload.*' => 'file|mimes:jpg,jpeg,pdf',
        'land_and_buildings_on_date' => 'required',
        'land_and_buildings_as_on_date_upload' => 'required',
        'land_and_buildings_as_on_date_upload.*' => 'file|mimes:jpg,jpeg,pdf',
        'knowledge_partner_and_innovatorrelationship' => 'required|array',
        'knowledge_partner_and_innovatorrelationship.*' => 'required',
    ];

    /**
     * @param KnowledgePartnerInterface $knowledgePartnerRepository
     */
    public function __construct(KnowledgePartnerInterface $knowledgePartnerRepository)
    {
        $this->knowledgePartnerRepository = $knowledgePartnerRepository;
    }

    /**
     * @param KnowledgePartnerRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postSendKnowledgePartner(Request $request, BaseHttpResponse $response)
    {
        $validation_messages_arr = [
            'name_of_the_institution.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.name_of_the_institution') . trans('plugins/knowledge-partner::knowledge-partner.form.name_of_the_institution.required'),
            'office_address.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.office_address') . trans('plugins/knowledge-partner::knowledge-partner.form.office_address.required'),
            'state_id.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.state') . trans('plugins/knowledge-partner::knowledge-partner.form.state.required'),
            'district.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.district') . trans('plugins/knowledge-partner::knowledge-partner.form.district.required'),
            'pin_code.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.pin_code') . trans('plugins/knowledge-partner::knowledge-partner.form.pin_code.required'),
            'pin_code.integer' => trans('plugins/knowledge-partner::knowledge-partner.tables.pin_code') . trans('plugins/knowledge-partner::knowledge-partner.form.pin_code.integer'),
            'institution_registered_under_or_accredited_by.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.institution_registered_under_or_accredited_by') . trans('plugins/knowledge-partner::knowledge-partner.form.institution_registered_under_or_accredited_by.required'),
            'year_of_establishment_or_inception.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.year_of_establishment_or_inception') . trans('plugins/knowledge-partner::knowledge-partner.form.year_of_establishment_or_inception.required'),
            'year_of_establishment_or_inception.integer' => trans('plugins/knowledge-partner::knowledge-partner.tables.year_of_establishment_or_inception') . trans('plugins/knowledge-partner::knowledge-partner.form.year_of_establishment_or_inception.integer'),
            'upload_proof_of_registration_upload.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.upload_proof_of_registration') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.required'),
            'upload_proof_of_registration_upload.*.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.upload_proof_of_registration') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.required'),
            'upload_proof_of_registration_upload.*.mimes' => trans('plugins/knowledge-partner::knowledge-partner.tables.upload_proof_of_registration') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.mimes'),
            'gst_no.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.gst_no') . trans('plugins/knowledge-partner::knowledge-partner.form.gst_no.required'),
            'pan.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.pan') . trans('plugins/knowledge-partner::knowledge-partner.form.pan.required'),
            'tin.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.tin') . trans('plugins/knowledge-partner::knowledge-partner.form.tin.required'),
            'board_founders_director.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.board_founders_director') . trans('plugins/knowledge-partner::knowledge-partner.form.board_founders_director.required'),
            'years_of_experience_in_incubation.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_incubation') . trans('plugins/knowledge-partner::knowledge-partner.form.years_of_experience_in_incubation.required'),
            'years_of_experience_in_incubation.integer' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_incubation') . trans('plugins/knowledge-partner::knowledge-partner.form.years_of_experience_in_incubation.integer'),
            'ceo.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.ceo') . trans('plugins/knowledge-partner::knowledge-partner.form.ceo.required'),
            'lead_scientist_or_incubation_manager.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.lead_scientist_or_incubation_manager') . trans('plugins/knowledge-partner::knowledge-partner.form.lead_scientist_or_incubation_manager.required'),
            'years_of_experience_in_r_and_d.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_r_and_d') . trans('plugins/knowledge-partner::knowledge-partner.form.years_of_experience_in_r_and_d.required'),
            'years_of_experience_in_r_and_d.integer' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_r_and_d') . trans('plugins/knowledge-partner::knowledge-partner.form.years_of_experience_in_r_and_d.integer'),
            'sources_of_financial_support.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.sources_of_financial_support') . trans('plugins/knowledge-partner::knowledge-partner.form.sources_of_financial_support.required'),
            'contact_person.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.contact_person') . trans('plugins/knowledge-partner::knowledge-partner.form.contact_person.required'),
            'mobile_number.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.mobile_number') . trans('plugins/knowledge-partner::knowledge-partner.form.mobile_number.required'),
            'email_address.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.email_address') . trans('plugins/knowledge-partner::knowledge-partner.form.email_address.required'),
            'email_address.email' => trans('plugins/knowledge-partner::knowledge-partner.tables.email_address') . trans('plugins/knowledge-partner::knowledge-partner.form.email_address.email'),
            'website.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.website') . trans('plugins/knowledge-partner::knowledge-partner.form.website.required'),
            
            'sectors_core_competencies.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.sectors_core_competencies') . trans('plugins/knowledge-partner::knowledge-partner.form.sectors_core_competencies.required'),
            'sectors_core_competencies.other' => trans('plugins/knowledge-partner::knowledge-partner.tables.sectors_core_competencies') . trans('plugins/knowledge-partner::knowledge-partner.form.sectors_core_competencies.other'),
            
            'have_testing_lab_facilities.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.have_testing_lab_facilities') . trans('plugins/knowledge-partner::knowledge-partner.form.have_testing_lab_facilities.required'),
            'type_of_lab.required_if' => trans('plugins/knowledge-partner::knowledge-partner.tables.type_of_lab') . trans('plugins/knowledge-partner::knowledge-partner.form.type_of_lab.required'),
            'area_in_sqft.required_if' => trans('plugins/knowledge-partner::knowledge-partner.tables.area_in_sqft') . trans('plugins/knowledge-partner::knowledge-partner.form.area_in_sqft.required'),
            'area_in_sqft.numeric' => trans('plugins/knowledge-partner::knowledge-partner.tables.area_in_sqft') . trans('plugins/knowledge-partner::knowledge-partner.form.area_in_sqft.numeric'),
            'equipments_upload.required_if' => trans('plugins/knowledge-partner::knowledge-partner.tables.equipments') . trans('plugins/knowledge-partner::knowledge-partner.form.equipments.required'),
            'equipments_upload.*.mimes' => trans('plugins/knowledge-partner::knowledge-partner.tables.equipments') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.mimes'),
            'lab_facility.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.lab_facility') . trans('plugins/knowledge-partner::knowledge-partner.form.lab_facility.required'),
            'service_charge.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.service_charge') . trans('plugins/knowledge-partner::knowledge-partner.form.service_charge.required'),
            'service_charge.numeric' => trans('plugins/knowledge-partner::knowledge-partner.tables.service_charge') . trans('plugins/knowledge-partner::knowledge-partner.form.service_charge.numeric'),
            'concessions.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.concessions') . trans('plugins/knowledge-partner::knowledge-partner.form.concessions.required'),
            
            'number_of_awareness_training_programs.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_awareness_training_programs') . trans('plugins/knowledge-partner::knowledge-partner.form.number_of_awareness_training_programs.required'),
            'number_of_awareness_training_programs.integer' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_awareness_training_programs') . trans('plugins/knowledge-partner::knowledge-partner.form.number_of_awareness_training_programs.integer'),
            'number_of_boot_camp_ideation_etc.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc') . trans('plugins/knowledge-partner::knowledge-partner.form.number_of_boot_camp_ideation_etc.required'),
            'number_of_boot_camp_ideation_etc.integer' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc') . trans('plugins/knowledge-partner::knowledge-partner.form.number_of_boot_camp_ideation_etc.integer'),
            'number_of_boot_camp_ideation_etc_files_upload.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc_files') . trans('plugins/knowledge-partner::knowledge-partner.form.number_of_boot_camp_ideation_etc_files.required'),
            'number_of_boot_camp_ideation_etc_files_upload.*.mimes' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc_files') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.mimes'),
            'have_you_accelerated_startups.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.have_you_accelerated_startups') . trans('plugins/knowledge-partner::knowledge-partner.form.have_you_accelerated_startups.required'),
            'total_number_of_startups_supported.required_if' => trans('plugins/knowledge-partner::knowledge-partner.tables.total_number_of_startups_supported') . trans('plugins/knowledge-partner::knowledge-partner.form.total_number_of_startups_supported.required'),
            'total_number_of_startups_supported.integer' => trans('plugins/knowledge-partner::knowledge-partner.tables.total_number_of_startups_supported') . trans('plugins/knowledge-partner::knowledge-partner.form.total_number_of_startups_supported.integer'),
            'ivp_applications_and_sanctions_upload.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.ivp_applications_and_sanctions') . trans('plugins/knowledge-partner::knowledge-partner.form.ivp_applications_and_sanctions.required'),
            'ivp_applications_and_sanctions_upload.*.mimes' => trans('plugins/knowledge-partner::knowledge-partner.tables.ivp_applications_and_sanctions') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.mimes'),
            
            'total_no_of_mentors_available.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.total_no_of_mentors_available') . trans('plugins/knowledge-partner::knowledge-partner.form.total_no_of_mentors_available.required'),
            'total_no_of_mentors_available.integer' => trans('plugins/knowledge-partner::knowledge-partner.tables.total_no_of_mentors_available') . trans('plugins/knowledge-partner::knowledge-partner.form.total_no_of_mentors_available.integer'),
            'mentor_details.required_unless' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_details') . trans('plugins/knowledge-partner::knowledge-partner.form.mentor_details.required'),
            'mentor_details.*.required_unless' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_details') . trans('plugins/knowledge-partner::knowledge-partner.form.mentor_details.required'),
            'mentor_details.*.*.required_unless' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_details') . trans('plugins/knowledge-partner::knowledge-partner.form.mentor_details.required'),
            
            'number_of_technologies_commercialized.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_technologies_commercialized') . trans('plugins/knowledge-partner::knowledge-partner.form.number_of_technologies_commercialized.required'),
            'number_of_indian_or_wipo_compliant_patents_received.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_indian_or_wipo_compliant_patents_received') . trans('plugins/knowledge-partner::knowledge-partner.form.number_of_indian_or_wipo_compliant_patents_received.required'),
            
            'financial_support_received_for_innovators_upload.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.financial_support_received_for_innovators') . trans('plugins/knowledge-partner::knowledge-partner.form.financial_support_received_for_innovators.required'),
            'financial_support_received_for_innovators_upload.*.mimes' => trans('plugins/knowledge-partner::knowledge-partner.tables.financial_support_received_for_innovators') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.mimes'),
            'your_financial_status_upload.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.your_financial_status') . trans('plugins/knowledge-partner::knowledge-partner.form.your_financial_status.required'),
            'your_financial_status_upload.*.mimes' => trans('plugins/knowledge-partner::knowledge-partner.tables.your_financial_status') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.mimes'),
            'land_and_buildings_on_date.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.land_and_buildings_on_date') . trans('plugins/knowledge-partner::knowledge-partner.form.land_and_buildings_on_date.required'),
            'land_and_buildings_as_on_date_upload.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.land_and_buildings_as_on_date') . trans('plugins/knowledge-partner::knowledge-partner.form.land_and_buildings_as_on_date.required'),
            'land_and_buildings_as_on_date_upload.*.mimes' => trans('plugins/knowledge-partner::knowledge-partner.tables.land_and_buildings_as_on_date') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.mimes'),
            'knowledge_partner_and_innovatorrelationship.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.knowledge_partner_and_innovatorrelationship') . trans('plugins/knowledge-partner::knowledge-partner.form.knowledge_partner_and_innovatorrelationship.required'),
            'knowledge_partner_and_innovatorrelationship.*.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.knowledge_partner_and_innovatorrelationship') . trans('plugins/knowledge-partner::knowledge-partner.form.knowledge_partner_and_innovatorrelationship.required'),
        ];
        
        try {
            $input_data = $request->all();
            $validation_arr = $this->step1_validation_arr;
            if(isset($input_data['step']) && in_array($input_data['step'], ['1', '3', '4', '5', '6', '7']))
            {
                $validation_arr_name = 'step'.$input_data['step'].'_validation_arr';
                $validation_arr = $this->$validation_arr_name;
                //$validation_arr = [];
                $validation_flag = true;
            }
            else if(isset($input_data['step']) && $input_data['step'] == 2)
            {
                $validation_arr = $this->step2_validation_arr;
                $validation_arr = [];
                $validation_flag = false;
                $error_message = $validation_messages_arr['sectors_core_competencies.required'];
                if(isset($input_data['sectors_core_competencies']) && !empty($input_data['sectors_core_competencies']))
                {
                    $validation_flag = true;
                    if(in_array("other", $input_data['sectors_core_competencies']) && ((!isset($input_data['sectors_core_competencies_other'])) || (isset($input_data['sectors_core_competencies_other']) && $input_data['sectors_core_competencies_other'] == "")))
                    {
                        $validation_flag = false;
                        $error_message = $validation_messages_arr['sectors_core_competencies.other'];
                    }
                }
            }
            //else if(isset($input_data['step']) && $input_data['step'] == 7)
            //{
            //    $validation_arr = $this->step7_validation_arr;
            //    //$validation_arr = [];
            //    $validation_flag = true;
            //}
            $validator = \Validator::make($input_data, $validation_arr, $validation_messages_arr);
    		if ($validator->fails() || !$validation_flag) {
    		    $error_content = '<h4>Data valiadtion fail</h4>';
        		$error_arr = json_decode($validator->errors(), true);
    		    if(!empty($error_arr))
    		    {
        		    foreach($error_arr as $error_key => $error_val)
        		    {
        		        $error_content .= $error_val[0] . '<br/>';
        		    }
    		    }
    		    else if(isset($error_message) && $error_message != "")
    		    {
    		        $error_content .= $error_message . '<br/>';
    		    }
    		    $return_data = [
                    'message' => 'Data valiadtion failed.',
                    'message_type' => 'validation_error',
                    'content' => $error_content,
                    //'error_arr' => $input_data['sectors_core_competencies'],
                    //'error_message' => $error_message,
                ];
    		}
        	else if ($validator->passes()) {
        	    if(isset($input_data['step']) && $input_data['step'] == 7)
        	    {
            	    $data_arr = $input_data;
            	    $sectors_core_competencies = $mentor_details = $knowledge_partner_and_innovatorrelationship = [];
            	    if(isset($input_data['sectors_core_competencies']) && !empty($input_data['sectors_core_competencies']))
            	    {
            	        $sectors_core_competencies = $input_data['sectors_core_competencies'];
            	        if(in_array('other' , $sectors_core_competencies) && isset($input_data['sectors_core_competencies_other']) && $input_data['sectors_core_competencies_other'] != "")
            	        {
            	            $sectors_core_competencies['other'] = $input_data['sectors_core_competencies_other'];
            	        }
            	    }
            	    if(isset($input_data['total_no_of_mentors_available']) && $input_data['total_no_of_mentors_available'] > 0 && isset($input_data['mentor_details']) && !empty($input_data['mentor_details']))
            	    {
            	        for($i = 0; $i < $input_data['total_no_of_mentors_available']; $i++)
            	        {
            	            $mentor_details[] = [
            	                'mentor_name' => isset($input_data['mentor_details']['mentor_name']) && isset($input_data['mentor_details']['mentor_name'][$i]) ? $input_data['mentor_details']['mentor_name'][$i] : '',
            	                'mentor_qualification' => isset($input_data['mentor_details']['mentor_qualification']) && isset($input_data['mentor_details']['mentor_qualification'][$i]) ? $input_data['mentor_details']['mentor_qualification'][$i] : '',
            	                'mentor_designation' => isset($input_data['mentor_details']['mentor_designation']) && isset($input_data['mentor_details']['mentor_designation'][$i]) ? $input_data['mentor_details']['mentor_designation'][$i] : '',
            	                'mentor_date_of_joining_your_organization' => isset($input_data['mentor_details']['mentor_date_of_joining_your_organization']) && isset($input_data['mentor_details']['mentor_date_of_joining_your_organization'][$i]) ? $input_data['mentor_details']['mentor_date_of_joining_your_organization'][$i] : '',
            	                'mentor_number_of_years_experience' => isset($input_data['mentor_details']['mentor_number_of_years_experience']) && isset($input_data['mentor_details']['mentor_number_of_years_experience'][$i]) ? $input_data['mentor_details']['mentor_number_of_years_experience'][$i] : '',
            	                'mentor_how_many_innovators_has_guided_so_far' => isset($input_data['mentor_details']['mentor_how_many_innovators_has_guided_so_far']) && isset($input_data['mentor_details']['mentor_how_many_innovators_has_guided_so_far'][$i]) ? $input_data['mentor_details']['mentor_how_many_innovators_has_guided_so_far'][$i] : '',
            	            ];
            	        }
            	    }
            	    if(isset($input_data['knowledge_partner_and_innovatorrelationship']) && !empty($input_data['knowledge_partner_and_innovatorrelationship']))
            	    {
            	        $knowledge_partner_and_innovatorrelationship = $input_data['knowledge_partner_and_innovatorrelationship'];
            	    }
            	    $data_arr['sectors_core_competencies'] = json_encode($sectors_core_competencies);
            	    $data_arr['mentor_details'] = json_encode($mentor_details);
            	    $data_arr['knowledge_partner_and_innovatorrelationship'] = json_encode($knowledge_partner_and_innovatorrelationship);
            	    
            	    $file_upload_fields = ['upload_proof_of_registration_upload', 'key_recognition_award_received_by_institute_upload', 'certifications_upload', 'equipments_upload', 'number_of_boot_camp_ideation_etc_files_upload', 'ivp_applications_and_sanctions_upload', 'financial_support_received_for_innovators_upload', 'your_financial_status_upload', 'land_and_buildings_as_on_date_upload'];
            	    $allowed_extention_arr = ['jpg', 'jpeg', 'pdf'];
            	    foreach($file_upload_fields as $field_key => $field_val)
            	    {
            	        $table_field_name = str_replace(['_upload'], [''], $field_val);
            	        $field_data_arr = [];
            	        if(isset($input_data[$field_val]) && !empty($input_data[$field_val]))
            	        {
            	            foreach($input_data[$field_val] as $file_key => $file_val)
            	            {
            	                if($file_val->getError() == 0 && $file_val->getClientOriginalName() != "" && in_array($file_val->getClientOriginalExtension(), $allowed_extention_arr))
            	                {
            	                    $file_extention = "." . $file_val->getClientOriginalExtension();
            	                    $file_name = $file_val->getClientOriginalName();
            	                    $file_name = str_replace([$file_extention], [''], $file_name);
            	                    $new_name = preg_replace("/[^A-Za-z0-9]/", '', $file_name);
            	                    $field_data_arr[] = $new_name . '_' . rand(999, 999999) . '' . $file_extention; //'knowledge-partner/' . 
            	                }
            	            }
            	        }
            	        $data_arr[$table_field_name] = json_encode($field_data_arr);
            	    }
            	    $knowledge_partner = $this->knowledgePartnerRepository->getModel();
            	    $knowledge_partner->fill($data_arr);
            	    $saved_data = $this->knowledgePartnerRepository->createOrUpdate($knowledge_partner);
            	    if($saved_data)
    		        {
    		            foreach($file_upload_fields as $field_key => $field_val)
                	    {
                	        $table_field_name = str_replace(['_upload'], [''], $field_val);
                	        $file_name_arr = json_decode($data_arr[$table_field_name], true);
                	        $i = 0;
                	        $field_data_arr = [];
                	        if(isset($input_data[$field_val]) && !empty($input_data[$field_val]))
                	        {
                	            foreach($input_data[$field_val] as $file_key => $file_val)
                	            {
                	                if($file_val->getError() == 0 && $file_val->getClientOriginalName() != "" && in_array($file_val->getClientOriginalExtension(), $allowed_extention_arr))
                	                {
                	                    //$store_file_name = str_replace(['knowledge-partner/'], [''], $file_name_arr[$i]);
                	                    $file_val->move(public_path('storage/knowledge-partner'), $file_name_arr[$i]);
                	                    $i++;
                	                }
                	            }
                	        }
                	    }
                        $return_data = [
                            //'post_data' => $input_data,
                            'message' => 'Data has been saved successfully',
                            'message_type' => 'success',
                            //'data_arr' => $data_arr,
                        ];
    		        }
    		        else
    		        {
                        $return_data = [
                            'message' => 'We are having some problem. Please try later.',
                            'message_type' => 'error',
                        ];
    		        }
        	    }
        	    else
        	    {
                    $return_data = [
                        'message' => 'Step ' . (isset($input_data['step']) && $input_data['step'] != '' ? $input_data['step'] : '1') . ' validation successful.',
                        'message_type' => 'validation_success',
                    ];
        	    }
    		}
			return response()->json($return_data);
        } catch (Exception $exception) {
            info($exception->getMessage());
            $return_data = [
                'code' => 400,
                'message' => $exception->getMessage(),
            ];
			return response()->json($return_data);
        }
    }
}
