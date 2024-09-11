<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ParishRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $parish = new \Impiger\MasterDetail\Models\Parish;
		$parish = ($this->parish) ? $parish::find($this->parish) : $this;
        $validationRules = [
            'name'=>'required|unique:parish,name,'.$this->parish.',id,deleted_at,NULL',
	'sub_county_id'=>'required',
	
            
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
        return ['name.required'=>'The Parish Name field is required.',
	'name.unique'=>'The Parish Name has already been taken.',
	'sub_county_id.required'=>'The Subcounty Name field is required.',
	
            ];
    }
}
