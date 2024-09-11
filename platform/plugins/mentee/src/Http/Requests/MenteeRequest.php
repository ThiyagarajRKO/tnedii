<?php

namespace Impiger\Mentee\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MenteeRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $mentee = new \Impiger\Mentee\Models\Mentee;
		$mentee = ($this->mentee) ? $mentee::find($this->mentee) : $this;
        $validationRules = [
            'entrepreneur_id'=>'required',
            'mentor_id'=>'required',
            'industry_id'=>'required',
            'specialization_id'=>'required',
            'experience_id'=>'required',
            'last_use_id'=>'required',
            'proficiency_level_id'=>'required',
            'qualification_id'=>'required',
            'description'=>'required',
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
