<?php

namespace Impiger\Vendor\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class VendorRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $vendor = new \Impiger\Vendor\Models\Vendor;
		$vendor = ($this->vendor) ? $vendor::find($this->vendor) : $this;
        $validationRules = [
            'name'=>'required',
            'pia_constitution_id'=>'required',
            'pia_mainactivity_id'=>'required',
            'email'=>'required|email:filter|unique:users,email,'.$vendor->user_id.',id,deleted_at,NULL|unique:impiger_users,email,'.$vendor->user_id.',id,deleted_at,NULL',
            'password'=>'required',
            'contact_number'=>'required|max:10|min:10|unique:vendors,contact_number,'.$vendor->id.',id,deleted_at,NULL',
            'address'=>'required',
            'district_id'=>'required',
            'pincode'=>'required',
            'date_of_establishment'=>'required',
            'name_principal'=>'required',
            'seating_capacity'=>'required',
            'audio_video'=>'required',
            'rest_dining'=>'required',
            'access_distance'=>'required',
            'accommodation_facility'=>'required',
            'refreshment_provision'=>'required',
            'experience_year'=>'required',
            'achivements'=>'required',
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
