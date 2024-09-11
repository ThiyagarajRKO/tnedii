<?php

namespace Impiger\InnovationVoucherProgram\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class IvpKnowledgePartnerRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $ivpKnowledgePartner = new \Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner;
		$ivpKnowledgePartner = ($this->ivpKnowledgePartner) ? $ivpKnowledgePartner::find($this->ivpKnowledgePartner) : $this;
        $validationRules = [
            'organization_type'=>'required',
	'organization_name'=>'required',
	'contact_person'=>'required',
	'designation'=>'required',
	'mobile_number'=>'required',
	'email_id'=>'required',
	'responsibilities'=>'required',
	'attachment'=>'required',
	
            
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
        return ['organization_type.required'=>'This field is required',
	'organization_name.required'=>'This field is required',
	'contact_person.required'=>'This field is required',
	'designation.required'=>'This field is required',
	'mobile_number.required'=>'This field is required',
	'email_id.required'=>'This field is required',
	
            ];
    }
}
