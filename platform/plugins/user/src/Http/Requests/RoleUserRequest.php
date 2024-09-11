<?php

namespace Impiger\User\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class RoleUserRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validationRules = [
            'role_id'=>'sometimes|required',
	
            
        ];
        
        if (setting('enable_captcha') && is_plugin_active('captcha')) {
            $validationRules = array_merge($validationRules,[]);
        }
        return $validationRules;
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return ['role_id.required'=>'The Role field is required.',
	
            ];
    }
}
