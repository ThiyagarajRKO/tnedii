<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SubcountyRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $subcounty = new \Impiger\MasterDetail\Models\Subcounty;
		$subcounty = ($this->subcounty) ? $subcounty::find($this->subcounty) : $this;
        $validationRules = [
            'name'=>'required|unique:subcounty,name,'.$this->subcounty.',id,deleted_at,NULL',
	'county_id'=>'required',
	
            
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
        return ['name.required'=>'The Subcounty Name field is required.',
	'name.unique'=>'The Subcounty Name has already been taken.',
	'county_id.required'=>'The County Name field is required.',
	
            ];
    }
}
