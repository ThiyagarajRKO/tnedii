<?php

return [
    'menu'                  => 'Knowledge Partner',
    'edit'                  => 'View Knowledge Partner',
    'view'      => 'View Knowledge Partner',
    'tables'                => [
        'name_of_the_institution' => 'Name of the institution',
        'district' => 'District',
        'state' => 'State',
        'pin_code' => 'Pin code',
        'gst_no' => 'GST no',
        'pan' => 'PAN',
        'tin' => 'TIN',
        'contact_person' => 'Contact person',
        'mobile_number' => 'Mobile number',
        'email_address' => 'Email address',
        'website' => 'Website',
        'office_address' => 'Office address',
        'institution_registered_under_or_accredited_by' => 'Institution Registered under / accredited by',
        'year_of_establishment_or_inception' => 'Year of establishment/Inception',
        'upload_proof_of_registration' => 'Upload Proof of Registration',
        'board_founders_director' => 'Board/Founders/Director',
        'years_of_experience_in_incubation' => 'Years of Experience in Incubation',
        'ceo' => 'C.E.O.',
        'lead_scientist_or_incubation_manager' => 'Lead Scientist / Incubation Manager',
        'years_of_experience_in_r_and_d' => 'Years of Experience in R&D',
        'sources_of_financial_support' => 'Sources of Financial Support',
        'key_recognition_award_received_by_institute' => 'Key recognition Award Received by Institute',
        'certifications' => 'Certifications',
        'sectors_core_competencies' => 'Sectors  / core competencies of the institution',
        'research_development' => 'Research & Development',
        'have_testing_lab_facilities' => 'Do have testing Lab Facilities?',
        'type_of_lab' => 'Type of Lab',
        'area_in_sqft' => 'Area in Sq.Ft',
        'equipments' => 'Equipment\'s',
        'lab_facility' => 'Describe the lab facility which you are ready to share with the innovators on priority basis.',
        'service_charge' => 'What is the service charge you collect from the innovators for the service offered?',
        'concessions' => 'Describe the concessions offered to innovators for using your lab facilities',
        'facilities_available' => 'Facilities available for Innovation/Research',
        'number_of_awareness_training_programs' => 'Number of Entrepreneurship awareness/training programs conducted in the last three fiscal years',
        'number_of_boot_camp_ideation_etc' => 'Number of boot camp/ideation/design thinking/business proposal/business pitch workshop conducted',
        'number_of_boot_camp_ideation_etc_files' => 'Number of boot camp/ideation/design thinking/business proposal/business pitch workshop conducted Documents',
        'have_you_accelerated_startups' => 'Have you accelerated/funded startups in the last three fiscal years?',
        'total_number_of_startups_supported' => 'Total number of startups supported in the last three fiscal years',
        'ivp_applications_and_sanctions' => 'IVP applications & sanctions in the last 3 years',
        'technical_support_and_mentorship' => 'Technical support and mentorship  available in the institution',
        'total_no_of_mentors_available' => 'Total no of mentors available',
        'mentor_name' => 'Name',
        'mentor_qualification' => 'Qualification',
        'mentor_designation' => 'Designation',
        'mentor_date_of_joining_your_organization' => 'Date of joining your organization',
        'mentor_number_of_years_experience' => 'Number of years experience',
        'mentor_how_many_innovators_has_guided_so_far' => 'How many innovators he/she has guided so far.',
        'ipr_related_registrations' => 'IPR related Registrations',
        'number_of_technologies_commercialized' => 'Number of technologies commercialized in past 5 years',
        'number_of_indian_or_wipo_compliant_patents_received' => 'Number of Indian or WIPO-compliant patents received in last 5 years',
        'financial_support_received_for_innovators' => 'Financial support received for innovators',
        'your_financial_status' => 'Please let us know your financial status',
        'land_and_buildings_on_date' => 'Land and buildings as on Date',
        'land_and_buildings_as_on_date' => 'Land and buildings as on Date Documents',
        'knowledge_partner_and_innovatorrelationship' => 'Knowledge Partner and InnovatorRelationship',
        'mentor_details' => 'Technical support and mentorship details',
        '' => '',
        '' => '',
    ],
    'knowledge_partner_information'   => 'Knowledge partner information',
    'replies'               => 'Replies',
    'email'                 => [
        'header'  => 'Email',
        'title'   => 'New knowledge partner from your site',
        'success' => 'Send message successfully!',
        'failed'  => 'Can\'t send message on this time, please try again later!',
    ],
    'form'                  => [
        'name_of_the_institution'    => [
            'required' => ' is required',
        ],
        'office_address'    => [
            'required' => ' is required',
        ],
        'state'    => [
            'required' => ' is required',
        ],
        'district'    => [
            'required' => ' is required',
        ],
        'pin_code'    => [
            'required' => ' is required',
            'integer' => ' must be numeric',
        ],
        'institution_registered_under_or_accredited_by'    => [
            'required' => ' is required',
        ],
        'year_of_establishment_or_inception'    => [
            'required' => ' is required',
            'integer' => ' must be numeric',
        ],
        'upload_proof_of_registration'    => [
            'required' => ' is required',
            'mimes' => ' only jpg, jpeg, pdf extention are allowed',
        ],
        'gst_no'    => [
            'required' => ' is required',
        ],
        'pan'    => [
            'required' => ' is required',
        ],
        'tin'    => [
            'required' => ' is required',
        ],
        'board_founders_director'    => [
            'required' => ' is required',
        ],
        'years_of_experience_in_incubation'    => [
            'required' => ' is required',
            'integer' => ' must be numeric',
        ],
        'ceo'    => [
            'required' => ' is required',
        ],
        'lead_scientist_or_incubation_manager'    => [
            'required' => ' is required',
        ],
        'years_of_experience_in_r_and_d'    => [
            'required' => ' is required',
            'integer' => ' must be numeric',
        ],
        'sources_of_financial_support'    => [
            'required' => ' is required',
        ],
        'contact_person'    => [
            'required' => ' is required',
        ],
        'mobile_number'    => [
            'required' => ' is required',
        ],
        'email_address'    => [
            'required' => ' is required',
            'email'    => ' is not valid',
        ],
        'website'    => [
            'required' => ' is required',
        ],
        
        'sectors_core_competencies'    => [
            'required' => ' is required',
            'other' => ' please specify other',
        ],
        
        'have_testing_lab_facilities'    => [
            'required' => ' is required',
        ],
        'type_of_lab'    => [
            'required' => ' is required',
        ],
        'area_in_sqft'    => [
            'required' => ' is required',
            'numeric' => ' must be numeric',
        ],
        'equipments'    => [
            'required' => ' is required',
        ],
        'lab_facility'    => [
            'required' => ' is required',
        ],
        'service_charge'    => [
            'required' => ' is required',
            'numeric' => ' must be numeric',
        ],
        'concessions'    => [
            'required' => ' is required',
        ],
        
        'number_of_awareness_training_programs'    => [
            'required' => ' is required',
            'integer' => ' must be numeric',
        ],
        'number_of_boot_camp_ideation_etc'    => [
            'required' => ' is required',
            'integer' => ' must be numeric',
        ],
        'number_of_boot_camp_ideation_etc_files'    => [
            'required' => ' is required',
        ],
        'have_you_accelerated_startups'    => [
            'required' => ' is required',
        ],
        'total_number_of_startups_supported'    => [
            'required' => ' is required',
            'integer' => ' must be numeric',
        ],
        'ivp_applications_and_sanctions'    => [
            'required' => ' is required',
        ],
        
        'total_no_of_mentors_available'    => [
            'required' => ' is required',
            'integer' => ' must be numeric',
        ],
        'mentor_details'    => [
            'required' => ' is required',
        ],
        
        'number_of_technologies_commercialized'    => [
            'required' => ' is required',
        ],
        'number_of_indian_or_wipo_compliant_patents_received'    => [
            'required' => ' is required',
        ],
        
        'financial_support_received_for_innovators'    => [
            'required' => ' is required',
        ],
        'your_financial_status'    => [
            'required' => ' is required',
        ],
        'land_and_buildings_on_date'    => [
            'required' => ' is required',
        ],
        'land_and_buildings_as_on_date'    => [
            'required' => ' is required',
        ],
        'knowledge_partner_and_innovatorrelationship'    => [
            'required' => ' is required',
        ],
    ],
    'knowledge_partner_sent_from'     => 'This knowledge partner information sent from',
    'sender'                => 'Sender',
    'sender_email'          => 'Email',
    'sender_address'        => 'Address',
    'sender_phone'          => 'Phone',
    'message_content'       => 'Message content',
    'sent_from'             => 'Email sent from',
    'form_name'             => 'Name',
    'form_email'            => 'Email',
    'form_address'          => 'Address',
    'form_subject'          => 'Subject',
    'form_phone'            => 'Phone',
    'form_message'          => 'Message',
    'required_field'        => 'The field with (<span style="color: red">*</span>) is required.',
    'send_btn'              => 'Send message',
    'new_msg_notice'        => 'You have <span class="bold">:count</span> New Messages',
    'view_all'              => 'View all',
    'statuses'              => [
        'read'   => 'Read',
        'unread' => 'Unread',
    ],
    'phone'                 => 'Phone',
    'address'               => 'Address',
    'message'               => 'Message',
    'settings'              => [
        'email' => [
            'title'       => 'Knowledge Partner',
            'description' => 'Knowledge partner email configuration',
            'templates'   => [
                'notice_title'       => 'Send notice to administrator',
                'notice_description' => 'Email template to send notice to administrator when system get new knowledge partner',
            ],
        ],
    ],
    'no_reply'              => 'No reply yet!',
    'reply'                 => 'Reply',
    'send'                  => 'Send',
    'shortcode_name' => 'Knowledge partner form',
    'shortcode_description' => 'Add a knowledge partner form',
    'shortcode_content_description' => 'Add shortcode [knowledge-partner-form][/knowledge-partner-form] to editor?',
    'message_sent_success'  => 'Message sent successfully!',
];
