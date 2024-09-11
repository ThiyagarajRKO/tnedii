<?php

namespace Impiger\KnowledgePartner\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\KnowledgePartner\Enums\KnowledgePartnerStatusEnum;
use Impiger\Base\Models\BaseModel;

class KnowledgePartner extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'knowledge_partners';

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_of_the_institution', 'office_address', 'state_id', 'district', 'district_id', 'pin_code', 'institution_registered_under_or_accredited_by', 'year_of_establishment_or_inception', 'upload_proof_of_registration', 'gst_no', 'pan', 'tin', 'board_founders_director', 'years_of_experience_in_incubation', 'ceo', 'lead_scientist_or_incubation_manager', 'years_of_experience_in_r_and_d', 'sources_of_financial_support', 'key_recognition_award_received_by_institute', 'certifications', 'contact_person', 'mobile_number', 'email_address', 'website', 'sectors_core_competencies', 'have_testing_lab_facilities', 'type_of_lab', 'area_in_sqft', 'equipments', 'lab_facility', 'service_charge', 'concessions', 'number_of_awareness_training_programs', 'number_of_boot_camp_ideation_etc', 'number_of_boot_camp_ideation_etc_files', 'have_you_accelerated_startups', 'total_number_of_startups_supported', 'ivp_applications_and_sanctions', 'total_no_of_mentors_available', 'mentor_details', 'number_of_technologies_commercialized', 'number_of_indian_or_wipo_compliant_patents_received', 'financial_support_received_for_innovators', 'your_financial_status', 'land_and_buildings_on_date', 'land_and_buildings_as_on_date', 'knowledge_partner_and_innovatorrelationship', 'created_by', 'updated_by', 'deleted_at', 'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        //'status' => KnowledgePartnerStatusEnum::class,
    ];

}
