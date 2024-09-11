<?php

namespace Impiger\TrainingTitle\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class OnlineTrainingSessionRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $onlineTrainingSession = new \Impiger\TrainingTitle\Models\OnlineTrainingSession;
		$onlineTrainingSession = ($this->onlineTrainingSession) ? $onlineTrainingSession::find($this->onlineTrainingSession) : $this;
        $validationRules = [
            'header'=>'required',
	'title'=>'required',
	'url'=>'required|url',
	'type'=>'required',
	
            
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
