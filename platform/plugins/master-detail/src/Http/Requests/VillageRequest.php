<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class VillageRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $village = new \Impiger\MasterDetail\Models\Village;
		$village = ($this->village) ? $village::find($this->village) : $this;
        $validationRules = [
            'name'=>'required|unique:village,name,'.$this->village.',id,deleted_at,NULL',
	'parish_id'=>'required',
	
            
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
        return ['name.required'=>'The Village Name field is required.',
	'name.unique'=>'The Village Name has already been taken.',
	'parish_id.required'=>'The Parish Name field is required.',
	
            ];
    }
}
