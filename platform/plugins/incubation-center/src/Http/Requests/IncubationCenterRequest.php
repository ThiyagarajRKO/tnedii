<?php

namespace Impiger\IncubationCenter\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class IncubationCenterRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $incubationCenter = new \Impiger\IncubationCenter\Models\IncubationCenter;
		$incubationCenter = ($this->incubationCenter) ? $incubationCenter::find($this->incubationCenter) : $this;
        $validationRules = [
            'district_id'=>'required',
	'center_name'=>'required',
	'establishment_date'=>'required',
	'no_of_incubatees'=>'required',
	
            
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
