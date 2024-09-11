<?php

namespace Impiger\TnsiStartup\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class TnsiStartupRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $tnsiStartup = new \Impiger\TnsiStartup\Models\TnsiStartup;
		$tnsiStartup = ($this->tnsiStartup) ? $tnsiStartup::find($this->tnsiStartup) : $this;
        $validationRules = [
            'region_id'=>'required',
            'hub_institution_id'=>'required',
            'spoke_registration_id'=>'required',
	'name'=>'required',
	'idea_about'=>'required',
	'is_your_idea'=>'required',
	'about_startup'=>'required',
	'problem_of_address'=>'required',
	'solution_of_problem'=>'required',
	'unique_selling_proposition'=>'required',
	'revenue_stream'=>'required',
	'description'=>'required',
	'duration'=>'required',
	
            
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
        return ['spoke_registration_id.required'=>'The Name of the college field is required',
	'name.required'=>'The Name of your Startup field is required',
	'idea_about.required'=>'This field is required',
	'is_your_idea.required'=>'This Field is required',
	'about_startup.required'=>'This field is required',
	'problem_of_address.required'=>'This field is required',
	'solution_of_problem.required'=>'This field is required',
	'unique_selling_proposition.required'=>'This field is required',
	'revenue_stream.required'=>'This field is required',
	'description.required'=>'This field is required',
	'duration.required'=>'This field is required',
	
            ];
    }
}
