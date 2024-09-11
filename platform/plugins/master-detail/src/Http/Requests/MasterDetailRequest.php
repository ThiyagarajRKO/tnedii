<?php

namespace Impiger\MasterDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MasterDetailRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $masterDetail = new \Impiger\MasterDetail\Models\MasterDetail;
		$masterDetail = ($this->masterDetail) ? $masterDetail::find($this->masterDetail) : $this;
        $validationRules = [
            'attribute'=>'required',
	'name'=>'required|unique:attribute_options,name,'.$masterDetail->id.',id,attribute,'.$masterDetail->attribute.',deleted_at,NULL',
	
            
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
