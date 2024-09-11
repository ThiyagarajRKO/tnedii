<?php

namespace Impiger\User\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UserAddressRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $userAddress = new \Impiger\User\Models\UserAddress;
		$userAddress = ($this->userAddress) ? $userAddress::find($this->userAddress) : $this;
        $validationRules = [
	    'present_phone'=>'nullable|numeric|digits_between:7,11',

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
	'present_phone.numeric'=>'The Contact Number must be a number.',
	'present_phone.digits_between'=>'The Contact Number must be between 7 and 11 digits.',
	            ];
    }
}
