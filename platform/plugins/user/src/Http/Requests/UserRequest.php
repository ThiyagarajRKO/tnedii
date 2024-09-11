<?php

namespace Impiger\User\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UserRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = new \Impiger\User\Models\User;
		$user = ($this->user) ? $user::find($this->user) : $this;
        $validationRules = [
            'email'=>'sometimes|required|email:filter|unique:impiger_users,email,'.$user->id.',id,deleted_at,NULL|unique:users,email,'.$user->user_id.',id,deleted_at,NULL',
            'first_name'=>'required',
            'username'=>'sometimes|required|unique:impiger_users,username,'.$user->id.',id,deleted_at,NULL',
            'user_addresses.present_phone'=>'nullable|min:5|regex:/^[0-9*\+-]+$/',
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
        return ['email.required'=>' The Email ID field is required.',
	'email.email'=>'The Email ID must be a valid email address.',
	'email.unique'=>'The Email ID has already been taken.',
	'user_addresses.present_phone.min'=>'The Contact Number must be at least 5 characters.',
	'user_addresses.present_phone.regex'=>'The Contact Number format is invalid(ex:042389021/+971 042389021).',
	

		];
    }
}
