<?php

namespace Impiger\Workflows\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Arr;

class WorkflowsRequest extends Request
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $workflows = new \Impiger\Workflows\Models\Workflows;
        $workflows = ($this->workflows) ? $workflows::find($this->workflows) : $this;
        
        $validation = [];
        for($i=0; $i < 25; $i++) {
            $fromState = Arr::get($workflows->transitions, $i.'.from_state');
            $toState = Arr::get($workflows->transitions, $i.'.to_state');
            $validation['transitions.'.$i.'.name']= "sometimes|required";
            $validation['transitions.'.$i.'.from_state'] = "sometimes|required|not_in:".$toState;
            $validation['transitions.'.$i.'.to_state'] = "sometimes|required|not_in:".$fromState;
        }
        
        $validationRules = [
            'name' => 'sometimes|required|unique:workflows,name,'.$workflows->id,
            'module_controller' => 'required',
            'module_property' => 'required',
            'initial_state' => 'required',
        ];
        $validationRules = $validationRules + $validation;
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
            'name.required' => 'The workflow title field is required.',
            'transitions.*.name.required' => 'The transition name field is required',
            'transitions.*.from_state.required' => 'The from state field is required',
            'transitions.*.to_state.required' => 'The to state field is required.',
            'transitions.*.from_state.not_in' => 'The selected transitions from state is invalid.',
            'transitions.*.to_state.not_in' => 'The selected transitions to state is invalid.',
        ];
    }
}
