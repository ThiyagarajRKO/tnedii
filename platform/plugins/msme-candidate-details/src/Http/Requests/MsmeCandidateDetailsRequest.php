<?php

namespace Impiger\MsmeCandidateDetails\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MsmeCandidateDetailsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $msmeCandidateDetails = new \Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails;
		$msmeCandidateDetails = ($this->msmeCandidateDetails) ? $msmeCandidateDetails::find($this->msmeCandidateDetails) : $this;
        $validationRules = [
            'scheme'=>'required',
	'candidate_msme_ref_id'=>'required',
	'email'=>'required',
	'mobile_no'=>'required',
            
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
