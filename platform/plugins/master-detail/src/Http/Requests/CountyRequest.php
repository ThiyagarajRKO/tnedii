<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CountyRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $county = new \Impiger\MasterDetail\Models\County;
		$county = ($this->county) ? $county::find($this->county) : $this;
        $validationRules = [
            'name'=>'required|unique:county,name,'.$this->county.',id,deleted_at,NULL',
	'district_id'=>'required',
	
            
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
        return ['name.required'=>'The County Name field is required.',
	'name.unique'=>'The County Name has already been taken.',
	'district_id.required'=>'The District Name field is required.',
	
            ];
    }
}
