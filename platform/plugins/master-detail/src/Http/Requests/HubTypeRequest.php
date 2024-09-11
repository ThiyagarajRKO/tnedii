<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class HubTypeRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $hubType = new \Impiger\MasterDetail\Models\HubType;
		$hubType = ($this->hubType) ? $hubType::find($this->hubType) : $this;
        $validationRules = [
            'hub_type'=>'required|unique:hub_types,hub_type,'.$this->hubType.',id,deleted_at,NULL',
	'hub_type_code'=>'required',
	
            
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
