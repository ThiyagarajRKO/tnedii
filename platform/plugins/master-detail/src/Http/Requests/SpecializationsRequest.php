<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SpecializationsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $specializations = new \Impiger\MasterDetail\Models\Specializations;
		$specializations = ($this->specializations) ? $specializations::find($this->specializations) : $this;
        $validationRules = [
            'name'=>'required|unique:specializations,name,'.$this->specializations.',id,deleted_at,NULL',
	'industry_id'=>'required',
	
            
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
        return ['industry_id.required'=>'The Industry field is required',
	
            ];
    }
}
