<?php

namespace Impiger\ACL\Http\Requests;

use Impiger\Support\Http\Requests\Request;

class CreateUserRequest extends Request
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
            'first_name'            => 'required|max:60|min:2',
            'last_name'             => 'required|max:60|min:2',
            'email'                 => 'required|max:60|min:6|email|unique:users',
            'username'              => 'required|min:4|max:30|unique:users',
        ];
        if(!is_plugin_active('password-criteria')){
            return [
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ] + $rules;
        }
        return $rules;
    }
}
