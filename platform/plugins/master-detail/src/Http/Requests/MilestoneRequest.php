<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MilestoneRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $milestone = new \Impiger\MasterDetail\Models\Milestone;
		$milestone = ($this->milestone) ? $milestone::find($this->milestone) : $this;
        $validationRules = [
            'milestone_name'=>'required',
	
            
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
