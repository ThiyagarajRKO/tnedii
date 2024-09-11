<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class DistrictRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $district = new \Impiger\MasterDetail\Models\District;
		$district = ($this->district) ? $district::find($this->district) : $this;
        $validationRules = [
            'name'=>'required|unique:district,name,'.$this->district.',id,deleted_at,NULL',
	'country_id'=>'required',
	'region_id'=>'required',
	
            
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
        return ['name.required'=>'The District Name field is required.',
	'name.unique'=>'The District Name has already been taken.',
	'country_id.required'=>' The country Name field is required.',
	'region_id.required'=>'The region field is required',
	
            ];
    }
}
