<?php

namespace Impiger\AnnualActionPlan\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AnnualActionPlanRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $annualActionPlan = new \Impiger\AnnualActionPlan\Models\AnnualActionPlan;
		$annualActionPlan = ($this->annualActionPlan) ? $annualActionPlan::find($this->annualActionPlan) : $this;
        $validationRules = [
            'name'=>'required',
	'financial_year_id'=>'required',
	'division_id'=>'required',
	'officer_incharge_designation_id'=>'required',
	'duration'=>'required',
	'no_of_batches'=>'required',
	'budget_per_program'=>'required',
	'total_budget'=>'required',
	'batch_size'=>'required',
	'training_module'=>'required',
	
            
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
