<?php

namespace Impiger\SimpleSlider\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SimpleSliderRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $slider = new \Impiger\SimpleSlider\Models\SimpleSlider;
		$slider = ($this->slider) ? $slider::find($this->slider) : $this;

        $validationRules = [            
            /* @Customized By Sabari Shankar Parthiban start
           'name'=>'required',*/
            'name'     => "required",
            /* @Customized By Sabari Shankar Parthiban end*/
            'key'=>'required|unique:simple_sliders,key,'.$this->simple_slider.',id',
            'status' => Rule::in(BaseStatusEnum::values()),
    ];  
        return $validationRules;
    }
    public function messages()
    {
        return [
            'key.unique'=>'The key field has already been taken.',

            ];
    }
}
