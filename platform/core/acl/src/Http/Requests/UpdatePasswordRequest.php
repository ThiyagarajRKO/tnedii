<?php

namespace Impiger\ACL\Http\Requests;

use Impiger\Support\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdatePasswordRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @customized Sabari Shankar.Parthiban 
     */
    public function rules()
    {
       
        $rules = [
            'password'              => 'required|min:6|max:60',
            'password_confirmation' => 'same:password',
        ];
        
        if(!is_plugin_active('password-criteria')){
            if (Auth::user() && Auth::user()->isSuperUser()) {
            return $rules;
        }

        return ['old_password' => 'sometimes|required|min:6|max:60'] + $rules;
        }
        return [];
    }
}
