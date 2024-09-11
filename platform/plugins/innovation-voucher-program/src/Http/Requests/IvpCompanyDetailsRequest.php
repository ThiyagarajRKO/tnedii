<?php

namespace Impiger\InnovationVoucherProgram\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class IvpCompanyDetailsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $ivpCompanyDetails = new \Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails;
		$ivpCompanyDetails = ($this->ivpCompanyDetails) ? $ivpCompanyDetails::find($this->ivpCompanyDetails) : $this;
        $validationRules = [
            'company_name' => 'required',
            'designation' => 'required',
            'company_address' => 'required',
            'company_classification' => 'required',
            'registration_number' => 'required',
            'registration_date' => 'required',
            
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
