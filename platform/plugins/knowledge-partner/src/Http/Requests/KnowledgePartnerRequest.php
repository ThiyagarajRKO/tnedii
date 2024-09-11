<?php

namespace Impiger\KnowledgePartner\Http\Requests;

use Impiger\Support\Http\Requests\Request;

class KnowledgePartnerRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function rules()
    {
        
        $rules = [
            //STEP 1 VALIDATION CHECK
            'name_of_the_institution' => 'required',
            'office_address' => 'required',
            'state_id' => 'required',
            'district' => 'required',
            'pin_code' => 'required|numeric',
            'institution_registered_under_or_accredited_by' => 'required',
            'year_of_establishment_or_inception' => 'required|numeric',
            'upload_proof_of_registration' => 'required|array',
            'upload_proof_of_registration.*' => 'required|mimes:jpg,jpeg,pdf',
            'gst_no' => 'required',
            'pan' => 'required',
            'tin' => 'required',
            'board_founders_director' => 'required',
            'years_of_experience_in_incubation' => 'required|numeric',
            'ceo' => 'required',
            'lead_scientist_or_incubation_manager' => 'required',
            'years_of_experience_in_r_and_d' => 'required|numeric',
            'sources_of_financial_support' => 'required',
            //'key_recognition_award_received_by_institute' => 'required',
            //'certifications' => 'required',
            'contact_person' => 'required',
            'mobile_number' => 'required',
            'email_address' => 'required|email',
            'website' => 'required',
            
            
            //STEP 2 VALIDATION CHECK
            "sectors_core_competencies"    => [
                'required',
                'array', // input must be an array
            ],
            
            //'email' => 'required|email',
            //'content' => 'required',
            //'phone' => 'nullable|numeric|digits_between:7,11'
        ];
        if (setting('enable_captcha') && is_plugin_active('captcha')) {
            $rules ['g-recaptcha-response'] = 'required|captcha';
        }
        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name_of_the_institution.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.name_of_the_institution') . trans('plugins/knowledge-partner::knowledge-partner.form.name_of_the_institution.required'),
            'office_address.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.office_address') . trans('plugins/knowledge-partner::knowledge-partner.form.office_address.required'),
            'state_id.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.state') . trans('plugins/knowledge-partner::knowledge-partner.form.state.required'),
            'district.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.district') . trans('plugins/knowledge-partner::knowledge-partner.form.district.required'),
            'pin_code.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.pin_code') . trans('plugins/knowledge-partner::knowledge-partner.form.pin_code.required'),
            'pin_code.numeric' => trans('plugins/knowledge-partner::knowledge-partner.tables.pin_code') . trans('plugins/knowledge-partner::knowledge-partner.form.pin_code.numeric'),
            'institution_registered_under_or_accredited_by.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.institution_registered_under_or_accredited_by') . trans('plugins/knowledge-partner::knowledge-partner.form.institution_registered_under_or_accredited_by.required'),
            'year_of_establishment_or_inception.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.year_of_establishment_or_inception') . trans('plugins/knowledge-partner::knowledge-partner.form.year_of_establishment_or_inception.required'),
            'year_of_establishment_or_inception.numeric' => trans('plugins/knowledge-partner::knowledge-partner.tables.year_of_establishment_or_inception') . trans('plugins/knowledge-partner::knowledge-partner.form.year_of_establishment_or_inception.numeric'),
            'upload_proof_of_registration.*.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.upload_proof_of_registration') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.required'),
            'upload_proof_of_registration.*.mimes' => trans('plugins/knowledge-partner::knowledge-partner.tables.upload_proof_of_registration') . trans('plugins/knowledge-partner::knowledge-partner.form.upload_proof_of_registration.mimes'),
            'gst_no.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.gst_no') . trans('plugins/knowledge-partner::knowledge-partner.form.gst_no.required'),
            'pan.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.pan') . trans('plugins/knowledge-partner::knowledge-partner.form.pan.required'),
            'tin.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.tin') . trans('plugins/knowledge-partner::knowledge-partner.form.tin.required'),
            'board_founders_director.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.board_founders_director') . trans('plugins/knowledge-partner::knowledge-partner.form.board_founders_director.required'),
            'years_of_experience_in_incubation.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_incubation') . trans('plugins/knowledge-partner::knowledge-partner.form.years_of_experience_in_incubation.required'),
            'years_of_experience_in_incubation.numeric' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_incubation') . trans('plugins/knowledge-partner::knowledge-partner.form.years_of_experience_in_incubation.numeric'),
            'ceo.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.ceo') . trans('plugins/knowledge-partner::knowledge-partner.form.ceo.required'),
            'lead_scientist_or_incubation_manager.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.lead_scientist_or_incubation_manager') . trans('plugins/knowledge-partner::knowledge-partner.form.lead_scientist_or_incubation_manager.required'),
            'years_of_experience_in_r_and_d.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_r_and_d') . trans('plugins/knowledge-partner::knowledge-partner.form.years_of_experience_in_r_and_d.required'),
            'years_of_experience_in_r_and_d.numeric' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_r_and_d') . trans('plugins/knowledge-partner::knowledge-partner.form.years_of_experience_in_r_and_d.numeric'),
            'sources_of_financial_support.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.sources_of_financial_support') . trans('plugins/knowledge-partner::knowledge-partner.form.sources_of_financial_support.required'),
            'contact_person.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.contact_person') . trans('plugins/knowledge-partner::knowledge-partner.form.contact_person.required'),
            'mobile_number.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.mobile_number') . trans('plugins/knowledge-partner::knowledge-partner.form.mobile_number.required'),
            'email_address.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.email_address') . trans('plugins/knowledge-partner::knowledge-partner.form.email_address.required'),
            'email_address.email' => trans('plugins/knowledge-partner::knowledge-partner.tables.email_address') . trans('plugins/knowledge-partner::knowledge-partner.form.email_address.email'),
            'website.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.website') . trans('plugins/knowledge-partner::knowledge-partner.form.website.required'),
            
            'sectors_core_competencies.required' => trans('plugins/knowledge-partner::knowledge-partner.tables.sectors_core_competencies') . trans('plugins/knowledge-partner::knowledge-partner.form.sectors_core_competencies.required'),
        ];
    }
}
