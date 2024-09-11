<?php

namespace Impiger\SpokeRegistration\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SpokeEcellsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $spokeEcells = new \Impiger\SpokeRegistration\Models\SpokeEcells;
		$spokeEcells = ($this->spokeEcells) ? $spokeEcells::find($this->spokeEcells) : $this;
        $validationRules = [
            'spoke_registration_id'=>\App\Utils\CrudHelper::customValidationRules("required|unique:spoke_ecells,spoke_registration_id,deleted_at",$spokeEcells),
	'name'=>\App\Utils\CrudHelper::customValidationRules("required|unique:spoke_ecells,name,spoke_registration_id,deleted_at",$spokeEcells),
	
            
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
        return ['spoke_registration_id.required'=>'The spoke institution is required',
	'spoke_registration_id.
unique'=>'The spoke registration has already been taken.',
	
            ];
    }
}
