<?php

namespace Impiger\Entrepreneur\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Arr;
class TraineeRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $attOption = \Impiger\MasterDetail\Models\MasterDetail::whereIn('attribute',["candidate_type", "student_type"])->pluck('id','slug')->toArray();
        $trainee = new \Impiger\Entrepreneur\Models\Trainee;
		$trainee = ($this->trainee) ? $trainee::find($this->trainee) : $this;

        // if($trainee && $trainee->entrepreneur_id) {
        //     $entrepreneur = \Impiger\Entrepreneur\Models\Entrepreneur::where('entrepreneur_id', $trainee->entrepreneur_id)->get()->first();
        //     if($entrepreneur) {
        //         $trainee->user_id = $entrepreneur->user_id;
        //     } else {
        //         $trainee->user_id = null;
        //     }
        // }

        $validationRules = [
            'division_id'=>'required',
            'financial_year_id'=>'required',
            'annual_action_plan_id'=>'required',
            'training_title_id'=> \App\Utils\CrudHelper::customValidationRules("required|unique:trainees,training_title_id,entrepreneur_id,deleted_at",$trainee),
            'email'=>'required|email:filter|unique:entrepreneurs,email,'.$trainee->entrepreneur_id.',id,deleted_at,NULL|unique:users,email,'.$trainee->user_id.',id,deleted_at,NULL',
            'password'=>'sometimes|required',
            'prefix_id'=>'required',
            'name'=>'required',
            'care_of'=>'required',
            'father_name'=>'required',
            'gender_id'=>'required',
            'dob'=>'required',
            'aadhaar_no'=>'nullable|numeric|digits:12',
            'mobile'=>'required|max:10|min:10|unique:entrepreneurs,mobile,'.$this->entrepreneur_id.',id,deleted_at,NULL',
            'physically_challenged'=>'required',
            'district_id'=>'required',
            'pincode'=>'required',
            'religion_id'=>'required',
            'community'=>'required',
            'candidate_type_id'=>'required',
            // 'student_type_id'=>'sometimes|required',
            // 'student_school_name'=>'sometimes|required',
            // 'student_standard_name'=>'sometimes|required',
            // 'student_college_name'=>'sometimes|required',
            // 'student_course_name'=>'sometimes|required',
            // 'hub_institution_id'=>'sometimes|required',
            // 'student_year'=>'sometimes|required',
            // 'spoke_registration_id'=>'sometimes|required',
            // 'qualification_id'=>'sometimes|required',
            'student_type_id'=>'sometimes|required_if:candidate_type_id,==,'.Arr::get($attOption, 'student-candidate-type'),
			'student_school_name'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'school-student-type'),
			'student_standard_name'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'school-student-type'),
			'student_college_name'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'college-student-type'),
			'student_course_name'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'college-student-type'),
			'hub_institution_id'=>'sometimes|required_if:candidate_type_id,==,'.Arr::get($attOption, 'spokestudent-candidate-type'),
			'student_year'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'college-student-type'),
			'spoke_registration_id'=>'sometimes|required_if:candidate_type_id,==,'.Arr::get($attOption, 'spokestudent-candidate-type'),
			'qualification_id'=>'sometimes|required_if:candidate_type_id,==,'.Arr::get($attOption, 'entrepreneur-candidate-type').','.Arr::get($attOption, 'startup-candidate-type').','.Arr::get($attOption, 'employed-candidate-type').','.Arr::get($attOption, 'unemployed-candidate-type'),
            
        ];
        
        
        return $validationRules;
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'training_title_id.unique' => 'Candidate is already mapped to selected taining program!'
        ];
    }
}
