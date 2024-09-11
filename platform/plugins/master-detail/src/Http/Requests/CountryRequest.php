<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CountryRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $country = new \Impiger\MasterDetail\Models\Country;
		$country = ($this->country) ? $country::find($this->country) : $this;
        $validationRules = [
            'nationality'=>'required|unique:countries,nationality,'.$this->country.',id,deleted_at,NULL',
	'country_code'=>'required|unique:countries,country_code,'.$this->country.',id,deleted_at,NULL',
	'country_name'=>'required|unique:countries,country_name,'.$this->country.',id,deleted_at,NULL',
	'phone_code'=>'required|unique:countries,phone_code,'.$this->country.',id,deleted_at,NULL',
	
            
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
