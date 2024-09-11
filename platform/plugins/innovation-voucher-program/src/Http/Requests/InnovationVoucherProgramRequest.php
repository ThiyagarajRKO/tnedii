<?php

namespace Impiger\InnovationVoucherProgram\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class InnovationVoucherProgramRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $innovationVoucherProgram = new \Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram;
        $innovationVoucherProgram = ($this->innovationVoucherProgram) ? $innovationVoucherProgram::find($this->innovationVoucherProgram) : $this;
        $validationRules = [
            'voucher_type' => 'required',
            'project_title' => \App\Utils\CrudHelper::customValidationRules("required|unique:innovation_voucher_programs,project_title,voucher_type,created_by,deleted_at", $innovationVoucherProgram),
            'mobile_number' => 'required',
            'email_id' => 'required',
            'problem_of_sector' => 'required',
            'scope_objective' => 'required',
            'outcomes_deliverables' => 'required',
            'role_of_knowledge_partner' => 'required',
            'budjet' => 'required',
            'team_capability' => 'required',
            'nature_of_innovation' => 'required',
            'impact' => 'required',
            'project_need' => 'required',
            'competetive' => 'required',
            'level_of_impact' => 'required',
            'capability_capacity' => 'required',
            'collabaration_with_knowledge_partner' => 'required',
            'pitch_for_your_project' => 'required',
            'project_based' => 'required',
            'main_sector' => 'required',
            'additional_sector' => 'required',
            'duration' => 'required',
            'project_cost' => 'required',
            'estimated_cost' => 'required',
            // 'state' => 'required',
            'district_id' => 'required',
            // 'identified_knowledge_partner' => 'required',
            'is_agree' => 'required',
            
            'ivp_company_details.company_name' => 'required',
            'ivp_company_details.designation' => 'required',
            'ivp_company_details.company_address' => 'required',
            'ivp_company_details.company_classification' => 'required',
            'ivp_company_details.registration_number' => 'required',
            'ivp_company_details.registration_date' => 'required',
            'ivp_company_details.certificate' => 'required',
            
            'ivp_knowledge_partners.organization_type' => 'required',
            'ivp_knowledge_partners.organization_name' => 'required',
            'ivp_knowledge_partners.contact_person' => 'required',
            'ivp_knowledge_partners.designation' => 'required',
            'ivp_knowledge_partners.mobile_number' => 'required',
            'ivp_knowledge_partners.email_id' => 'required',
            'ivp_knowledge_partners.responsibilities' => 'required',
            'ivp_knowledge_partners.attachment' => 'nullable',
        ];
        return $validationRules;
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return ['problem_of_sector.required' => 'This field is required',
            'scope_objective.required' => 'This field is required',
            'outcomes_deliverables.required' => 'This field is required',
            'role_of_knowledge_partner.required' => 'This field is required',
            'budjet.required' => 'This field is required',
            'team_capability.required' => 'This field is required',
            'impact.required' => 'This field is required',
            'level_of_impact.required' => 'This field is required',
            'capability_capacity.required' => 'This field is required',
            'collabaration_with_knowledge_partner.required' => 'This field is required',
            'pitch_for_your_project.required' => 'This field is required',
            'project_based.required' => 'This field is required',
            'main_sector.required' => 'This field is required',
            'additional_sector.required' => 'This field is required',
            'is_agree.required' => 'Check the declaration',
            
            'ivp_company_details.company_name.required' => 'This field is required',
            'ivp_company_details.designation.required' => 'This field is required',
            'ivp_company_details.company_address.required' => 'This field is required',
            'ivp_company_details.company_classification.required' => 'This field is required',
            'ivp_company_details.registration_number.required' => 'This field is required',
            'ivp_company_details.registration_date.required' => 'This field is required',
            'ivp_company_details.certificate.required' => 'This field is required',
            
            'ivp_knowledge_partners.organization_type.required' => 'This field is required',
            'ivp_knowledge_partners.organization_name.required' => 'This field is required',
            'ivp_knowledge_partners.contact_person.required' => 'This field is required',
            'ivp_knowledge_partners.designation.required' => 'This field is required',
            'ivp_knowledge_partners.mobile_number.required' => 'This field is required',
            'ivp_knowledge_partners.email_id.required' => 'This field is required',
            'ivp_knowledge_partners.attachment.required' => 'This field is required',
        ];
    }

}
