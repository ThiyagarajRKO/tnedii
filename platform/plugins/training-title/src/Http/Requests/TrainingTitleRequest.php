<?php

namespace Impiger\TrainingTitle\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class TrainingTitleRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $trainingTitle = new \Impiger\TrainingTitle\Models\TrainingTitle;
		$trainingTitle = ($this->trainingTitle) ? $trainingTitle::find($this->trainingTitle) : $this;
        $validationRules = [
            'division_id'=>'required',
	'financial_year_id'=>'required',
	'annual_action_plan_id'=>'required',
	'code'=>'required',
	'venue'=>'required',
	'phone'=>'required',
	'vendor_id'=>'required',
	'officer_incharge_designation_id'=>'required',
	'fee_paid'=>'required',
	'fee_amount'=>'sometimes|required',
	'private_workshop'=>'required',
	'training_start_date'=>'required|date|before_or_equal:training_end_date',
	'training_end_date'=>'required|date|after_or_equal:training_start_date',
	
            
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
