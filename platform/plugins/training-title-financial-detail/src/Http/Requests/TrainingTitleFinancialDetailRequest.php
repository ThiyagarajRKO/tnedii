<?php

namespace Impiger\TrainingTitleFinancialDetail\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class TrainingTitleFinancialDetailRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $trainingTitleFinancialDetail = new \Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail;
		$trainingTitleFinancialDetail = ($this->trainingTitleFinancialDetail) ? $trainingTitleFinancialDetail::find($this->trainingTitleFinancialDetail) : $this;
        $validationRules = [
            'division_id'=>'required',
	'financial_year_id'=>'required',
	'annual_action_plan_id'=>'required',
	'training_title_id'=>'required',
	'budget_approved'=>'required',
	'actual_expenditure'=>'required',
	'edi_admin_cost'=>'required',
	'revenue_generated'=>'required',
	
            
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
