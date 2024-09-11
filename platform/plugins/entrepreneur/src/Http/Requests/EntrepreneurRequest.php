<?php

namespace Impiger\Entrepreneur\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Arr;
class EntrepreneurRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
		$attOption = \Impiger\MasterDetail\Models\MasterDetail::whereIn('attribute',["candidate_type", "student_type"])->pluck('id','slug')->toArray();
        $entrepreneur = new \Impiger\Entrepreneur\Models\Entrepreneur;
		$entrepreneur = ($this->entrepreneur) ? $entrepreneur::find($this->entrepreneur) : $this;
        $validationRules = [
            'email'=>'required|email:filter|unique:entrepreneurs,email,'.$entrepreneur->id.',id,deleted_at,NULL|unique:users,email,'.$entrepreneur->user_id.',id,deleted_at,NULL',
			'password'=>'required',
			'prefix_id'=>'required',
			'name'=>'required',
			'care_of'=>'required',
			'father_name'=>'required',
			'gender_id'=>'required',
			'dob'=>'required',
			'aadhaar_no'=>'nullable|numeric|digits:12',
			'mobile'=>'required|max:10|min:10|unique:entrepreneurs,mobile,'.$entrepreneur->id.',id,deleted_at,NULL',
			'physically_challenged'=>'required',
			'district_id'=>'required',
			'pincode'=>'required',
			'religion_id'=>'required',
			'community'=>'required',
			'candidate_type_id'=>'required',
			'student_type_id'=>'sometimes|required_if:candidate_type_id,==,'.Arr::get($attOption, 'student-candidate-type'),
			'student_school_name'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'school-student-type'),
			'student_standard_name'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'school-student-type'),
			'student_college_name'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'college-student-type'),
			'student_course_name'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'college-student-type'),
			'hub_institution_id'=>'sometimes|required_if:candidate_type_id,==,'.Arr::get($attOption, 'spokestudent-candidate-type'),
			'student_year'=>'sometimes|required_if:student_type_id,==,'.Arr::get($attOption, 'college-student-type'),
			'spoke_registration_id'=>'sometimes|required_if:candidate_type_id,==,'.Arr::get($attOption, 'spokestudent-candidate-type'),
			'qualification_id'=>'sometimes|required_if:candidate_type_id,==,'.Arr::get($attOption, 'entrepreneur-candidate-type').','.Arr::get($attOption, 'startup-candidate-type').','.Arr::get($attOption, 'employed-candidate-type').','.Arr::get($attOption, 'unemployed-candidate-type'),
			'photo_path'=>'sometimes',
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
			"spoke_registration_id.required_if" => "The spoke registration id field is required when candidate type is Spoke Student",
			"student_school_name.required_if" => "The student school name field is required when student type is School",
			"student_standard_name.required_if" => "The student standard name field is required when student type is School",
			"student_college_name.required_if" => "The student college name field is required when student type is College",
			"student_course_name.required_if" => "The student course name field is required when student type is College",
			"student_year.required_if" => "The student year field is required when student type is College"
        ];
    }
}
