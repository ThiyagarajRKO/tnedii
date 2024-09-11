<?php

namespace Impiger\FinancialYear\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class FinancialYearRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $financialYear = new \Impiger\FinancialYear\Models\FinancialYear;
		$financialYear = ($this->financialYear) ? $financialYear::find($this->financialYear) : $this;
        $validationRules = [
        'session_start'=>'required|unique:financial_year,session_start,'.$this->financialYear.',id,deleted_at,NULL|valid_financial_year:session_end|valid_between_financial_year:'.$this->financialYear.',id,deleted_at',
        'session_end'=>'required|unique:financial_year,session_end,'.$this->financialYear.',id,deleted_at,NULL|valid_financial_year:session_start|valid_between_financial_year:'.$this->financialYear.',id,deleted_at',
        'is_running'=>'required|valid_running_year:'.$this->financialYear.',id,deleted_at,NULL',
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
        return ['is_running.unique'=>'Another session is in active state',
	
            ];
    }
}
