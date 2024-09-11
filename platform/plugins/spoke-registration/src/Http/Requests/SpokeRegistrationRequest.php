<?php

namespace Impiger\SpokeRegistration\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SpokeRegistrationRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $spokeRegistration = new \Impiger\SpokeRegistration\Models\SpokeRegistration;
		$spokeRegistration = ($this->spokeRegistration) ? $spokeRegistration::find($this->spokeRegistration) : $this;
        $validationRules = [

    'name'=>'required|unique:spoke_registration,name,'.$this->id.',id,deleted_at,NULL',
	'stream_of_institution'=>'required',
	'category'=>'required',
	'affiliation'=>'required',
	'hub_institution_id'=>'required',
	'year_of_establishment'=>'required',
	'locality_type'=>'required',
	'institute_state'=>'required',
	'program_level'=>'required',
	'address'=>'required',
	'pin_code'=>'required',
	'city'=>'required',
	'district_id'=>'required',
	'phone_no'=>'required|numeric|digits:10|unique:spoke_registration,phone_no,'.$this->id.',id,deleted_at,NULL',
	'email'=>'required|unique:spoke_registration,email,'.$this->id.',id,deleted_at,NULL',
            
            'head.name' => 'sometimes|required',
            'head.designation' => 'sometimes|required',
            'head.email' => 'sometimes|required|email:filter|unique:users,email,'.$this->id.',id,deleted_at,NULL|unique:impiger_users,email,'.$this->id.',id,deleted_at,NULL',
            'head.phone_number' => 'sometimes|required|numeric|digits:10|unique:impiger_users,phone_number,'.$this->id.',id,deleted_at,NULL',
             
            'faculty.name' => 'sometimes|required',
            'faculty.designation' => 'sometimes|required',
            'faculty.email' => 'sometimes|required|email:filter|unique:users,email,'.$this->id.',id,deleted_at,NULL|unique:impiger_users,email,'.$this->id.',id,deleted_at,NULL',
            'faculty.phone_number' => 'sometimes|required|numeric|digits:10|unique:impiger_users,phone_number,'.$this->id.',id,deleted_at,NULL',
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
            
            'head.name.required' => 'The name field is required.',
            'head.designation.required' => 'The designation field is required.',
            'head.email.required' => 'The email field is required.',
            'head.email.unique' => 'The email has already been taken.',
            'head.email.email' => 'The email must be a valid email address.',
            'head.phone_number.required' => 'The mobile number field is required.',
            'head.phone_number.unique' => 'The mobile number has already been taken.',
            
            'faculty.name.required' => 'The name field is required.',
            'faculty.designation.required' => 'The designation field is required.',
            'faculty.email.required' => 'The email field is required.',
            'faculty.email.unique' => 'The email has already been taken.',
            'faculty.email.email' => 'The email must be a valid email address.',
            'faculty.phone_number.required' => 'The mobile number field is required.',
            'faculty.phone_number.unique' => 'The mobile number has already been taken.'
        ];
    }
}
