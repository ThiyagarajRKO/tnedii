<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class RegionRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $region = new \Impiger\MasterDetail\Models\Region;
		$region = ($this->region) ? $region::find($this->region) : $this;
        $validationRules = [
            'name'=>'required|unique:regions,name,'.$this->region.',id,deleted_at,NULL',
	'code'=>'required|unique:regions,code,'.$this->region.',id,deleted_at,NULL',
	
            
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
