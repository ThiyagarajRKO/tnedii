<?php

namespace Impiger\HubInstitution\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class HubInstitutionRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $hubInstitution = new \Impiger\HubInstitution\Models\HubInstitution;
		$hubInstitution = ($this->hubInstitution) ? $hubInstitution::find($this->hubInstitution) : $this;
        $validationRules = [
            'hub_type_id'=>'required',
//	'hub_code'=>'required',
	'name'=>'required|unique:hub_institutions,name,'.$this->id.',id,deleted_at,NULL',
	'address'=>'required',
	'year_of_establishment'=>'required',
	'pincode'=>'required',
	
             'head.name' => 'sometimes|required',
            'head.designation' => 'sometimes|required',
            'head.email' => 'sometimes|required|email:filter|unique:users,email,'.$this->id.',id,deleted_at,NULL|unique:impiger_users,email,'.$this->id.',id,deleted_at,NULL',
            'head.phone_number' => 'sometimes|required|unique:impiger_users,phone_number,'.$this->id.',id,deleted_at,NULL',
             
            'faculty.name' => 'sometimes|required',
            'faculty.designation' => 'sometimes|required',
            'faculty.email' => 'sometimes|required|email:filter|unique:users,email,'.$this->id.',id,deleted_at,NULL|unique:impiger_users,email,'.$this->id.',id,deleted_at,NULL',
            'faculty.phone_number' => 'sometimes|required|unique:impiger_users,phone_number,'.$this->id.',id,deleted_at,NULL',
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
        return ['hub_type_id.required'=>'The Hub Type field is required',
                'head.name.required' => 'The name field is required.',
            'head.designation.required' => 'The designation field is required.',
            'head.email.required' => 'The email field is required.',
            'head.email.unique' => 'The email has already been taken.',
            'head.email.email' => 'The .email must be a valid email address.',
            'head.phone_number.required' => 'The mobile number field is required.',
            'head.phone_number.unique' => 'The mobile number has already been taken.',
            
            'faculty.name.required' => 'The name field is required.',
            'faculty.designation.required' => 'The designation field is required.',
            'faculty.email.required' => 'The email field is required.',
            'faculty.email.unique' => 'The email has already been taken.',
            'faculty.email.email' => 'The .email must be a valid email address.',
            'faculty.phone_number.required' => 'The mobile number field is required.',
            'faculty.phone_number.unique' => 'The mobile number has already been taken.',
            ];
    }
}
