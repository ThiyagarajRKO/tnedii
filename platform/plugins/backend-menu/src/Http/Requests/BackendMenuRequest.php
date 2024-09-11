<?php

namespace Impiger\BackendMenu\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class BackendMenuRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $backendMenu = new \Impiger\BackendMenu\Models\BackendMenu;
		$backendMenu = ($this->backendMenu) ? $backendMenu::find($this->backendMenu) : $this;
        $validationRules = [
            
            
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
        return [
            ];
    }
}
