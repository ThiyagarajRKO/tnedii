<?php

namespace Impiger\Mentor\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MentorRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $mentor = new \Impiger\Mentor\Models\Mentor;
		$mentor = ($this->mentor) ? $mentor::find($this->mentor) : $this;
        $validationRules = [
            'name'=>'required',
            'email'=>'required|email:filter|unique:mentors,email,'.$mentor->id.',id,deleted_at,NULL|unique:entrepreneurs,email,'.$mentor->entrepreneur_id.',id,deleted_at,NULL|unique:users,email,'.$mentor->user_id.',id,deleted_at,NULL',
            'password'=>'required',
            'vendor_id'=>'required',
            'district_id'=>'required',
            'industry_id'=>'required',
            'specialization_id'=>'required',
            'experience_id'=>'required',
            'last_use_id'=>'required',
            'proficiency_level_id'=>'required',
            'qualification_id'=>'required',
            'achivements'=>'required',
            'resume'=>'required',
            'status_id'=>'required',
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
            ];
    }
}
